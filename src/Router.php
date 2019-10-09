<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:32
 * $router->map(
 *      [
 *          'prefix' => '/prefix',
 *          'mask' => 'ca'
 *      ]
 *
 * )
 * 请求 -> 匹配 -> 前置中间件 -> 业务函数 -> 后置中间件 -> 响应
 */

namespace Aw\Routing;

use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Map\Cmr2Ncm;
use Aw\Routing\Matcher\Equal;
use Aw\Routing\Matcher\Group;
use Aw\Routing\Matcher\Ca;
use Aw\Routing\Matcher\Mca;
use Aw\Routing\Matcher\Method;
use Aw\Routing\Matcher\OrGroup;
use Aw\Routing\Matcher\Regexp;
use Aw\Routing\Route\AtCall;
use Aw\Routing\Route\Callback as CallbackRoute;
use Aw\Routing\Route\IRoute;
use Aw\Routing\Route\Ncm as NcmRoute;
use Closure;
use Exception;

class Router
{
    public $regexp = array(
        ':num' => '#^\d+$#',
        ':alpha' => '#^[a-zA-Z]+$#',
        ':var' => '#^[a-zA-Z]\w*$#',
    );
    protected $routes = array();

    protected $request;

    public $handle_404_callbacks = array();

    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function add404Handler(Closure $closure)
    {
        $this->handle_404_callbacks[] = $closure;
    }

    /**
     * @param $key
     * @param $regexp
     * @return $this
     */
    public function addRegexpPlaceholder($key, $regexp)
    {
        $this->regexp[$key] = $regexp;
        return $this;
    }

    /**
     * 判断url是否采用正则匹配
     * 0 equal
     * 1 regexp
     * 2 placeholder
     * @param $url
     * @return bool
     */
    public function isRegexpRoute($url)
    {
        if (substr($url, 0, 1) === Regexp::DELIMITER && substr($url, -1, 1) === Regexp::DELIMITER)
            return 1;
        foreach ($this->regexp as $key => $regexp) {
            if (strpos($url, $key) !== false)
                return 2;
        }
        return 0;
    }

    /**
     * url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配
     * action 支持callback 或者  \namespace\class@method
     * method 默认为index
     *
     * @param $url
     * @param $action
     * @param array $middleware
     * @return IRoute
     */
    public function get($url, $action, $middleware = array())
    {
        return $this->_request('get', $url, $action, $middleware);
    }

    /**
     * url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配
     * action 支持callback 或者  \namespace\class@method  method
     * 默认为index
     *
     * @param $url
     * @param $action
     * @param array $middleware
     * @return IRoute
     */
    public function post($url, $action, $middleware = array())
    {
        return $this->_request('post', $url, $action, $middleware);
    }

    /**
     * url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配
     * action 支持callback 或者  \namespace\class@method  method
     * 默认为index
     *
     * @param $url
     * @param $action
     * @param array $middleware
     * @return IRoute
     */
    public function delete($url, $action, $middleware = array())
    {
        return $this->_request('delete', $url, $action, $middleware);
    }


    /**
     * url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配
     * action 支持callback 或者  \namespace\class@method  method
     * 默认为index
     *
     * @param $url
     * @param $action
     * @param array $middleware
     * @return IRoute
     */
    public function put($url, $action, $middleware = array())
    {
        return $this->_request('put', $url, $action, $middleware);
    }

    /**
     * url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配
     * action 支持callback 或者  \namespace\class@method  method
     * 默认为index
     * method 可以支持数组  array('get','post')
     * @param $method
     * @param $url
     * @param $action
     * @param array $middleware
     * @return IRoute
     */
    public function any($url, $action, $middleware = array(), $method = "*")
    {
        if ($method === "*") {
            $method = array("get", "post", "delete", "put");
        }
        return $this->_request($method, $url, $action, $middleware);
    }

    /**
     * 使用CA映射到NCM方式
     * @param array $middleware
     * @return IRoute
     */
    public function ca($middleware = array())
    {
        $matcher = new Ca();
        $map = new Cmr2Ncm($matcher);
        $route = new NcmRoute($matcher, $map);
        return $this->add($route, $middleware);
    }

    /**
     * 使用MCA映射到NCM方式
     * @param array $middleware
     * @return IRoute
     */
    public function mca($middleware = array())
    {
        $matcher = new Mca();
        $map = new Cmr2Ncm($matcher);
        $route = new NcmRoute($matcher, $map);
        return $this->add($route, $middleware);
    }

    /**
     * 使用MCA映射到NCM方式
     * @param $regexp
     * @param array $middleware
     * @param null $map_callback
     * @return IRoute
     * @throws Exception
     */
    public function match($regexp, $middleware = array(), $map_callback = null)
    {
        if ($which = $this->isRegexpRoute($regexp)) {
            if ($which == 2) {
                $regexp = Regexp::DELIMITER . strtr($regexp, $this->regexp) . Regexp::DELIMITER;
            }
        } else {
            throw new Exception("invalid regexp");
        }
        $matcher = new Regexp($regexp);
        if ($map_callback instanceof Closure) {
//            $matcher->match($this->request);
            $map = $map_callback($matcher);
        } else {
            $map = new Cmr2Ncm($matcher);
        }
        $route = new NcmRoute($matcher, $map);
        return $this->add($route, $middleware);
    }

    /**
     * @param $method
     * @param $url
     * @param $action
     * @param $middleware
     * @return IRoute
     * @throws Exception
     */
    protected function _request($method, $url, $action, $middleware)
    {
        $matcher = new Group();
        if ($which = $this->isRegexpRoute($url)) {
            if ($which == 2) {
                $url = Regexp::DELIMITER . strtr($url, $this->regexp) . Regexp::DELIMITER;
            }
            $matcher->add(new Regexp($url));
        } else {
            $matcher->add(new Equal($url));
        }
        if (is_array($method)) {
            $or = new OrGroup();
            foreach ($method as $item) {
                $or->add(new Method($item));
            }
            $matcher->add($or);
        } else {
            $matcher->add(new Method($method));
        }

        if ($action instanceof Closure) {
            $route = new CallbackRoute($matcher, $action);
        } else if (is_string($action)) {
            $route = new AtCall($matcher, $action);
        } else {
            throw new Exception("invalid action");
        }
        return $this->add($route, $middleware);
    }

    /**
     * @return Response
     */
    public function run()
    {
        /**
         * @var IRoute $route
         */
        foreach ($this->routes as $routes) {
            $route = $routes['route'];
            $middleware = $routes['middleware'];
            if ($route->route($this->request, $middleware)) {
                return $route->getDispatchResult();
            }
        }
        $r = new Response('Page not found', 404);
        foreach ($this->handle_404_callbacks as $callback) {
            $callback($this->request);
        }
        return $r;
    }

    /**
     * @param IRoute $route
     * @param array $middleware
     * @return IRoute
     */
    public function add(IRoute $route, $middleware = array())
    {
        $this->routes[] = array(
            'route' => $route,
            'middleware' => $middleware,
        );
        return $route;
    }

    /**
     * Returns all routes in this collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->routes;
    }
}