<?php
/**
 * 本类职责是解析路由的第二个参数,执行 action
 * Ccontrol Action 存于Route的Option中
 * Created by PhpStorm.
 * User: awei.tian
 * Date: 9/20/17
 * Time: 8:03 PM
 */

namespace Tian\Route;
use \Tian\Http\Request;
use \Tian\Http\Response;
use Tian\Route\Exception\InvalidActionException;
use Tian\Route\Exception\MissingMandatoryParametersException;
use \Tian\Container;
use \Tian\Pipeline;
class ControllerResolver
{
    protected $defNamespace = "\\App\\Controller";


    /**
     * @var Container;
     */
    protected $container;

    /**
     * 用于索引
     * All of the short-hand keys for middlewares.
     * [
     *      wmname1 => m1,
     *      wmname2 => m2,
     * ]
     * @var array
     */
    protected $middleware = [];

    /**
     * 用于索引
     * All of the middleware groups.
     * [
     *      web:[
     *          mw1,
     *          mw2
     *      ],
     *
     * ]
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The priority-sorted list of middleware.
     * [
     *      mw1,
     *      mw2,
     *      ...
     * ]
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    public $middlewarePriority = [];
    /***
     * ControllerResolver constructor.
     * @param Container $container
     * @param array $middleware  middleware|middlewareGroups|middlewarePriority
     */
    public function __construct(Container $container,$middleware)
    {
        $this->container = $container;
        $this->middlewareGroups = $middleware['middlewareGroups'];
        $this->middleware = $middleware['middleware'];
        $this->middlewarePriority = $middleware['middlewarePriority'];
    }

    /**
     * 如果是MainController@Index格式的
     * 先处理NAMESPACE,可选参数_namespace
     *
     * @param Request $request
     * @param Route $route
     * @return Response
     */
    public function resolve(Route $route, Request $request)
    {
        return $this->middleware($route,$request);
    }

    /**
     * @param Route $route
     * @param Request $request
     * @return Response
     */
    protected function middleware(Route $route, Request $request)
    {
        $shouldSkipMiddleware = $this->container->bound('middleware.disable') &&
            $this->container->make('middleware.disable') === true;

        $middlewares = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        $action = $route->getOption("_call");
        if (array_key_exists("passMiddlewarePriority",$action))
        {
            $results = [];
        }
        else
        {
            $results = $this->middlewarePriority;
        }
        $this->middlewarePriority = [];
        foreach ($middlewares as $middleware)
        {
            if (is_callable($middleware))
            {
                $results[] = $middleware;
            }
            else if (is_string($middleware))
            {
                if (array_key_exists($middleware,$this->middleware))
                {
                    $results[] = $this->middleware[$middleware];
                }
                else if (array_key_exists($middleware,$this->middlewareGroups))
                {
                    $results = array_merge($results,$this->middlewareGroups[$middleware]);
                }
                else if (class_exists($middleware))
                {
                    $results[] = $middleware;
                }
            }
        }
        return (new Pipeline())
            ->send($request)
            ->through($results)
            ->then(function ($request) use ($route) {
                $res = $this->execAction(
                    $route,$request
                );
                return $res;
            });
    }

    protected function handleNamespace($call)
    {
        $namespace = isset($call["namespace"]) ? $call["namespace"] : $this->defNamespace;
        if ($namespace[0] != "\\")
        {
            $namespace = $this->defNamespace . "\\" . $namespace;
        }
        return $namespace;
    }

    /**
     * @param Route $route
     * @param Request $request
     * @return Response
     */
    protected function execAction(Route $route, Request $request)
    {
        $call = $route->getOption("_call");
        $this->container->instance('router.matched.action',$call);
        $this->container->instance('router.matched.route',$route);
        if (array_key_exists("uses", $call) || is_string($call[0])) {
            $callback = explode("@", isset($call['uses']) ? $call['uses'] : $call[0]);
            if ($callback[0][0] != "\\") {
                $namespace = $this->handleNamespace($call);
            } else {
                $namespace = $callback[0];
            }
            $class = $namespace;
            $method = $callback[1];
            if (!class_exists($class)) {
                $e = new InvalidActionException($class .' Not found.');
                $e->setClass($class);
                throw $e;
            }
            $rc = new \ReflectionMethod($class, $method);
            $arg = $this->resolverParameter($rc,$request);
//            var_dump($callback,$arg);exit;
            $content = call_user_func_array($callback[0]."::".$callback[1], $arg);
            return new Response($content);
        } elseif (is_callable($call[0])) {
            $rc = new \ReflectionFunction( $call[0] );
            $arg = $this->resolverParameter($rc,$request);
            $content = call_user_func_array($call[0], $arg);
            return new Response($content);
        }
        return new Response();
    }

    public function group(array $actions,\Closure $call)
    {

    }


    /**
     * @param Route $route
     * @return array
     */
    private function gatherRouteMiddleware(Route $route)
    {
        $action = $route->getOption("_call");
        if (array_key_exists("middleware",$action))
        {
            if (is_callable($action['middleware']))
            {
                return [$action['middleware']];
            }
            elseif (is_array($action['middleware']))
            {
                return $action['middleware'];
            }
        }
        return [];
    }

    /**
     * @param \ReflectionMethod|\ReflectionFunction $rc
     * @param Request $request
     *
     * @return array
     */
    private function resolverParameter($rc,$request)
    {
        $arg = [];
        foreach ($rc->getParameters() as $parameter) {
//            if ($parameter->getPosition() == 0)
//            {
//                //for first app
//                continue;
//            }
            if (!is_null($parameter->getClass()))
            {
                if ($parameter->getClass()->name == Container::class)
                {
                    $arg[] = $this->container;
                }
                else
                {
                    $arg[] = $this->container->make($parameter->getClass()->name);
//                    var_dump(current(array_reverse($arg)));exit;
                }
            }
            elseif ($request->attributes->has($parameter->getName()))
            {
                $arg[] = $request->attributes->get($parameter->getName());
            }
            else
            {
                throw new MissingMandatoryParametersException();
            }
        }
//        var_dump($arg);
        return $arg;
    }
}