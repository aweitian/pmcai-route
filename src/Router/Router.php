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

namespace Aw\Routing\Router;

use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Dispatch\IDispatcher;
use Aw\Routing\Matcher\IMatcher;
use Aw\Routing\Matcher\Mapca;
use Aw\Routing\Route;

class Router
{
    const TYPE_MATCHER_EQUAL = 0;
    const TYPE_MATCHER_STARTWITH = 1;
    const TYPE_MATCHER_REGEXP = 2;
    const TYPE_MATCHER_PMCAI = 3;
    const DEFAULT_CONTROL_NAMESPACE = "\\App\\Controller\\";
    protected $routes = array();
    protected $mw_defined = array();
    protected $mw_global = array();
    protected $request;
    /**
     * @var Middleware
     */
    protected $middleware;
    public $match_logs = array();

    /**
     * 问题能简单就简单化,不要想着运行期间改变全局中间件
     * Router constructor.
     * @param Request $request
     * @param array $middleware_defined
     * @param array $middleware_global
     */
    public function __construct(Request $request, $middleware_defined = array(), $middleware_global = array())
    {
        $this->request = $request;
        $this->mw_defined = $middleware_defined;
        $this->mw_global = $middleware_global;
        $this->middleware = new Middleware(array(
            "global" => $middleware_global,
            "defined" => $middleware_defined
        ));
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @return Route
     */
    public function get($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL)
    {
        return $this->request('GET', $pattern, $action, $middleware, $type);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @return Route
     */
    public function post($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL)
    {
        return $this->request('POST', $pattern, $action, $middleware, $type);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @return Route
     */
    public function put($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL)
    {
        return $this->request('PUT', $pattern, $action, $middleware, $type);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @return Route
     */
    public function delete($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL)
    {
        return $this->request('delete', $pattern, $action, $middleware, $type);
    }

    /**
     * @param string $prefix
     * @param array $middleware
     * @param array $dispatch_param namespace|namespace_map|ctl_tpl|act_tpl
     * @param array $matcher_param prefix|mask|moduleSkip|type
     * @param bool $useGlobalMiddleware
     * @return Route
     */
    public function pmcai($prefix = "/", $middleware = array(), $dispatch_param = array(), $matcher_param = array(), $useGlobalMiddleware = true)
    {
        $matcher_param["prefix"] = $prefix;
        $matcher = new Mapca(array_merge($matcher_param, array(
            "mask" => "ca",
            "type" => Mapca::TYPE_PMCAI
        )));
        //Route的ACTION参数传递数据类型过去,会被识别为pmcai Dispatcher
        $route = new Route($matcher, $this->middleware, $middleware, array_merge($dispatch_param, array(
            "namespace" => self::DEFAULT_CONTROL_NAMESPACE
        )), $useGlobalMiddleware);
        return $this->add($route);
    }

    /**
     * @param IMatcher $matcher
     * @param IDispatcher $dispatcher
     * @param array $middleware
     * @param bool $useGlobalMiddleware
     * @return Route
     */
    public function connect(IMatcher $matcher, IDispatcher $dispatcher, $middleware = array(), $useGlobalMiddleware = true)
    {
        $route = new Route($matcher, $this->middleware, $middleware, null, $useGlobalMiddleware);
        $route->setDispatcher($dispatcher);
        return $this->add($route);
    }

    /**
     * @param $method
     * @param $pattern
     * @param $action
     * @param $middleware
     * @param int $type TYPE_MATCHER_EQUAL|TYPE_MATCHER_REGEXP|TYPE_MATCHER_STARTWITH
     * @param bool $use_global_mw
     * @return Route
     */
    protected function request($method, $pattern, $action, $middleware, $type, $use_global_mw = true)
    {
        $matcher = MatcherFactory::CreateByMethodAndType($method, $type, $pattern);

        return $this->add(new Route($matcher, $this->middleware, $middleware, $action, $use_global_mw));
    }

    /**
     * @return Response
     */
    public function run()
    {
        /**
         * @var Route $route
         */
        foreach ($this->routes as $route) {
            if ($route->match($this->request)) {
                $route->setDispatcher(DispatcherFactory::CreateByAction($route->getAction()));
                $response = $route->route();
                if ($response instanceof Response) {
                    return $response;
                }
                return new Response($response);
            } else {
                $this->match_logs = array_merge($this->match_logs, $route->logs);
            }
        }
        return new Response('Page not found', 404);
    }

    /**
     * @param $name
     * @return Route
     */
    public function getRoute($name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    /**
     * Adds a route.
     *
     * @param string $name The route name
     * @param Route $route A Route instance
     * @return Route
     */
    public function add(Route $route, $name = null)
    {
        if (is_string($name)) {
            unset($this->routes[$name]);
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }
        return $route;
    }

    /**
     * Returns all routes in this collection.
     *
     * @return Route[] An array of routes
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * Adds a route collection at the end of the current set by appending all
     * routes of the added collection.
     *
     * @param Router $collection A RouteCollection instance
     */
    public function merge(Router $collection)
    {
        foreach ($collection->all() as $name => $route) {
            unset($this->routes[$name]);
            $this->routes[$name] = $route;
        }
    }
}