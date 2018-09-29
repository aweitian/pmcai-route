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
use Aw\Routing\Dispatch\PmcaiDispatcher;
use Aw\Routing\Matcher\AndCondition;
use Aw\Routing\Matcher\Callback;
use Aw\Routing\Matcher\IMatcher;
use Aw\Routing\Matcher\Mapca;
use Aw\Routing\Parse\Pmcai;
use Aw\Routing\Route;
use Closure;

class Router
{
    const TYPE_MATCHER_EQUAL = 0;
    const TYPE_MATCHER_STARTWITH = 1;
    const TYPE_MATCHER_REGEXP = 2;
    const TYPE_MATCHER_PMCAI = 3;

    protected $callback_router_matched = null;
    protected $callback_dispatcher_created = null;
    protected $callback_before_through_pre_middleware = null;
    protected $callback_after_through_pre_middleware = null;
    protected $callback_before_invoke_action = null;
    protected $callback_after_invoke_action = null;
    protected $callback_before_through_post_middleware = null;
    protected $callback_after_through_post_middleware = null;
    protected $callback_response_404 = null;


    protected $callback_response_500 = null;


    protected $routes = array();
    protected $mw_defined = array();
    protected $mw_global = array();
    protected $request;
    /**
     * @var Middleware
     */
    protected $middleware;
    public $match_logs = array();
    protected $callbacks = array();

    /**
     * 问题能简单就简单化,不要想着运行期间改变全局中间件
     * Router constructor.
     * @param Request $request
     * @param array $middleware_defined
     * @param array $middleware_global
     */
    public function __construct(Request $request = null, $middleware_defined = array(), $middleware_global = array())
    {
        $this->request = $request;
        $this->mw_defined = $middleware_defined;
        $this->mw_global = $middleware_global;
        $this->middleware = new Middleware(array(
            "global" => $middleware_global,
            "defined" => $middleware_defined
        ));
        $this->callbacks['ROUTER_MATCHED'] = null;
        $this->callbacks['DISPATCHER_CREATED'] = null;
    }

    /**
     * @return mixed
     */
    public function getCallbackRouterMatched()
    {
        return $this->callback_router_matched;
    }

    /**
     * @param mixed $callback_router_matched
     */
    public function setCallbackRouterMatched($callback_router_matched)
    {
        $this->callback_router_matched = $callback_router_matched;
    }

    /**
     * @return mixed
     */
    public function getCallbackDispatcherCreated()
    {
        return $this->callback_dispatcher_created;
    }

    /**
     * @param mixed $callback_dispatcher_created
     */
    public function setCallbackDispatcherCreated($callback_dispatcher_created)
    {
        $this->callback_dispatcher_created = $callback_dispatcher_created;
    }

    /**
     * @return mixed
     */
    public function getCallbackBeforeThroughPreMiddleware()
    {
        return $this->callback_before_through_pre_middleware;
    }

    /**
     * @param mixed $callback_before_through_pre_middleware
     */
    public function setCallbackBeforeThroughPreMiddleware($callback_before_through_pre_middleware)
    {
        $this->callback_before_through_pre_middleware = $callback_before_through_pre_middleware;
    }

    /**
     * @return mixed
     */
    public function getCallbackAfterThroughPreMiddleware()
    {
        return $this->callback_after_through_pre_middleware;
    }

    /**
     * @param mixed $callback_after_through_pre_middleware
     */
    public function setCallbackAfterThroughPreMiddleware($callback_after_through_pre_middleware)
    {
        $this->callback_after_through_pre_middleware = $callback_after_through_pre_middleware;
    }

    /**
     * @return mixed
     */
    public function getCallbackBeforeInvokeAction()
    {
        return $this->callback_before_invoke_action;
    }

    /**
     * @param mixed $callback_before_invoke_action
     */
    public function setCallbackBeforeInvokeAction($callback_before_invoke_action)
    {
        $this->callback_before_invoke_action = $callback_before_invoke_action;
    }

    /**
     * @return mixed
     */
    public function getCallbackAfterInvokeAction()
    {
        return $this->callback_after_invoke_action;
    }

    /**
     * @param mixed $callback_after_invoke_action
     */
    public function setCallbackAfterInvokeAction($callback_after_invoke_action)
    {
        $this->callback_after_invoke_action = $callback_after_invoke_action;
    }

    /**
     * @return mixed
     */
    public function getCallbackBeforeThroughPostMiddleware()
    {
        return $this->callback_before_through_post_middleware;
    }

    /**
     * @param mixed $callback_before_through_post_middleware
     */
    public function setCallbackBeforeThroughPostMiddleware($callback_before_through_post_middleware)
    {
        $this->callback_before_through_post_middleware = $callback_before_through_post_middleware;
    }

    /**
     * @return mixed
     */
    public function getCallbackAfterThroughPostMiddleware()
    {
        return $this->callback_after_through_post_middleware;
    }

    /**
     * @param mixed $callback_after_through_post_middleware
     */
    public function setCallbackAfterThroughPostMiddleware($callback_after_through_post_middleware)
    {
        $this->callback_after_through_post_middleware = $callback_after_through_post_middleware;
    }

    /**
     * @return mixed
     */
    public function getCallbackResponse404()
    {
        return $this->callback_response_404;
    }

    /**
     * @param mixed $callback_response_404
     */
    public function setCallbackResponse404($callback_response_404)
    {
        $this->callback_response_404 = $callback_response_404;
    }

    /**
     * @return mixed
     */
    public function getCallbackResponse500()
    {
        return $this->callback_response_500;
    }

    /**
     * @param mixed $callback_response_500
     */
    public function setCallbackResponse500($callback_response_500)
    {
        $this->callback_response_500 = $callback_response_500;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param Route $route
     * @param $new_name
     */
    public function onRouteNameChange(Route $route, $new_name)
    {
        $old_name = $route->getName();
        if (!isset($this->routes[$old_name])) {
            return;
        }
        $nr = array();
        foreach ($this->routes as $k => $r) {
            if ($r->getName() !== $old_name) {
                $nr[$k] = $r;
            } else {
                $nr[$new_name] = $r;
            }
        }
        $this->routes = $nr;
    }

    /**
     * 设置路由表项中的名字
     * @param $old_name
     * @param $new_name
     */
    public function setName($old_name, $new_name)
    {
        if (!isset($this->routes[$old_name])) {
            return;
        }
        $this->onRouteNameChange($this->routes[$old_name], $new_name);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    public function get($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL, $use_global_mw = true, $name = null)
    {
        return $this->request('GET', $pattern, $action, $middleware, $type, $use_global_mw, $name);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    public function post($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL, $use_global_mw = true, $name = null)
    {
        return $this->request('POST', $pattern, $action, $middleware, $type, $use_global_mw, $name);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    public function put($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL, $use_global_mw = true, $name = null)
    {
        return $this->request('PUT', $pattern, $action, $middleware, $type, $use_global_mw, $name);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    public function delete($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL, $use_global_mw = true, $name = null)
    {
        return $this->request('delete', $pattern, $action, $middleware, $type, $use_global_mw, $name);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @param int $type
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    public function any($pattern, $action, $middleware = array(), $type = self::TYPE_MATCHER_EQUAL, $use_global_mw = true, $name = null)
    {
        return $this->request('*', $pattern, $action, $middleware, $type, $use_global_mw, $name);
    }

    /**
     * @param string $prefix
     * @param array $middleware
     * @param array $dispatch_param namespace|namespace_map|ctl_tpl|act_tpl
     * @param array $matcher_param prefix|mask|moduleSkip|type|module|check_dispatch
     * @param bool $useGlobalMiddleware
     * @param string $name
     * @return Route
     */
    public function pmcai($prefix = "/", $middleware = array(), $dispatch_param = array(), $matcher_param = array(), $useGlobalMiddleware = true, $name = null)
    {
        $me = $this;
        $matcher_param["prefix"] = $prefix;
        $matcher = new Mapca(array_merge(array(
            "mask" => "ca",
            "type" => Mapca::TYPE_PMCAI
        ), $matcher_param));
        if (array_key_exists('check_dispatch', $matcher_param) && $matcher_param['check_dispatch'] === true) {
            $and_matcher = new AndCondition();
            $and_matcher->add($matcher);
            $and_matcher->add(new Callback(array(
                "callback" => function () use ($dispatch_param, $me) {
                    $request = $me->request;
                    $ca = array();
                    /**
                     * @var Pmcai $matcher
                     */
                    $matcher = $request->carry['matcher'];
                    $ca['module'] = $matcher->getModule();
                    $ca['ctl'] = $matcher->getControl();
                    $ca['act'] = $matcher->getAction();

                    $arg = array_merge($dispatch_param, $ca);
                    $ret = PmcaiDispatcher::isDispatchable($arg);
                    return $ret;
                }
            )));

            $matcher = $and_matcher;
        }
        //Route的ACTION参数传递数据类型过去,会被识别为pmcai Dispatcher
        $route = new Route($matcher, $this->middleware, $middleware, $dispatch_param, $useGlobalMiddleware);
        return $this->add($route, $name);
    }

    /**
     * @param IMatcher $matcher
     * @param IDispatcher $dispatcher
     * @param array $middleware
     * @param bool $useGlobalMiddleware
     * @param null $name
     * @return Route
     */
    public function connect(IMatcher $matcher, IDispatcher $dispatcher, $middleware = array(), $useGlobalMiddleware = true, $name = null)
    {
        $route = new Route($matcher, $this->middleware, $middleware, null, $useGlobalMiddleware);
        $route->setDispatcher($dispatcher);
        return $this->add($route, $name);
    }

    /**
     * @param $method
     * @param $pattern
     * @param $action
     * @param $middleware
     * @param int $type TYPE_MATCHER_EQUAL|TYPE_MATCHER_REGEXP|TYPE_MATCHER_STARTWITH
     * @param bool $use_global_mw
     * @param null $name
     * @return Route
     */
    protected function request($method, $pattern, $action, $middleware, $type, $use_global_mw = true, $name = null)
    {
        $matcher = MatcherFactory::CreateByMethodAndType($method, $type, $pattern);
        return $this->add(new Route($matcher, $this->middleware, $middleware, $action, $use_global_mw), $name);
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
                $c = $this->getCallbackRouterMatched();
                if (is_callable($c)) {
                    $c($route, $this->request, $this);
                }
                try {
                    $dispatch = DispatcherFactory::CreateByAction($route->getAction());
                    $c = $this->getCallbackDispatcherCreated();
                    if (is_callable($c)) {
                        $c($dispatch, $route, $this->request, $this);
                    }
                    $route->setDispatcher($dispatch);
                    $response = $route->route();
                    if ($response instanceof Response) {
                        return $response;
                    }
                    return new Response($response);
                } catch (\Exception $e) {
                    $c = $this->getCallbackResponse500();
                    $r = new Response($e->getMessage(), 500);
                    if (is_callable($c)) {
                        $c($r, $e, $route, $this->request, $this);
                    }
                    return $r;
                }
            } else {
                $this->match_logs = array_merge($this->match_logs, $route->logs);
            }
        }
        $c = $this->getCallbackResponse404();
        $r = new Response('Page not found', 404);
        if (is_callable($c)) {
            $c($r, $route, $this->request, $this);
        }
        return $r;
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
            $n = $name;
        } else {
            $n = count($this->routes);
            $this->routes[] = $route;
        }
        $route->setName($n);
        $route->setRouter($this);
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