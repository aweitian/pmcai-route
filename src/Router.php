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
use Aw\Routing\Matcher\Equal;
use Aw\Routing\Matcher\Group;
use Aw\Routing\Matcher\Method;
use Aw\Routing\Matcher\OrGroup;
use Aw\Routing\Matcher\Regexp;
use Aw\Routing\Route\AtCall;
use Aw\Routing\Route\Callback as CallbackRoute;
use Aw\Routing\Route\Callback;
use Aw\Routing\Route\IRoute;
use Closure;
use Exception;

class Router
{
    const URL_MODE_EQUAL = 0;
    const URL_MODE_PURE_REGEXP = 1;
    const URL_MODE_REGEXP_WITH_PLACEHOLDER = 2;

    public $regexp = array(
        ':num' => '(\d+)',
        ':alpha' => '([a-zA-Z]+)',
        ':var' => '([a-zA-Z]\w*)',
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
            return Router::URL_MODE_PURE_REGEXP;
        foreach ($this->regexp as $key => $regexp) {
            if (strpos($url, $key) !== false)
                return Router::URL_MODE_REGEXP_WITH_PLACEHOLDER;
        }
        return Router::URL_MODE_EQUAL;
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
        return $this->_request($method, $url, $action, $middleware);
    }

    /**
     * 使用MCA映射到NCM方式
     * @param $regexp
     * @param Closure|string $action
     * @param array $middleware
     * @return IRoute
     * @throws Exception
     */
    public function match($regexp, $action, $middleware = array())
    {
        if ($which = $this->isRegexpRoute($regexp)) {
            if ($which == Router::URL_MODE_REGEXP_WITH_PLACEHOLDER) {
                $regexp = Regexp::DELIMITER . strtr($regexp, $this->regexp) . Regexp::DELIMITER;
            }
        } else {
            throw new Exception("invalid regexp");
        }
        $matcher = new Regexp($regexp);
        if ($action instanceof Closure) {
            $route = new Callback($matcher, $action);
        } else {
            $route = new AtCall($matcher, $action);
        }
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
            if ($which == Router::URL_MODE_REGEXP_WITH_PLACEHOLDER) {
                $url = Regexp::DELIMITER . strtr($url, $this->regexp) . Regexp::DELIMITER;
            }
//            var_dump($url);
            $matcher->addUrlMatcher(new Regexp($url));
        } else {
            $matcher->addUrlMatcher(new Equal($url));
        }
        if (is_array($method)) {
            $or = new OrGroup();
            foreach ($method as $item) {
                $or->add(new Method($item));
            }
            $matcher->add($or);
        } else {
            if ($method !== "*") {
                $matcher->add(new Method($method));
            }
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