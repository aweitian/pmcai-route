<?php
/**
 * Ccontrol Action 存于Route的Option中
 * Created by PhpStorm.
 * User: awei.tian
 * Date: 9/20/17
 * Time: 8:03 PM
 */

namespace Tian\Route;
use \Tian\Http\Request;
//use Tian\Http\Request;
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


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 如果是MainController@Index格式的
     * 先处理NAMESPACE,可选参数_namespace
     *
     * @param Request $request
     * @param Route $route
     * @return Response
     */
    public function resolver(Route $route, Request $request)
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

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        return (new Pipeline())
            ->send($request)
            ->through($middleware)
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
        $action = $route->getOptions();
//        var_dump(array_keys($action));exit;
        $this->container->instance('route.matched.action',$action);
        if (array_key_exists("uses", $action) || (isset($action[0]) && is_string($action[0]))) {
            $callback = explode("@", isset($action['uses']) ? $action['uses'] : $action[0]);
            if ($callback[0][0] != "\\") {
                $namespace = $this->handleNamespace($action);
            } else {
                $namespace = $callback[0];
            }
            $class = $namespace;
            $method = $callback[1];
            if (!class_exists($class)) {
                $e = new InvalidActionException();
                $e->setClass($class);
                throw $e;
            }
            $rc = new \ReflectionMethod($class, $method);
            $arg = $this->resolverParameter($rc,$request);
//            var_dump($callback,$arg);exit;
            $rc = new \ReflectionClass($callback[0]);
            if ($rc->isSubclassOf("\\Tian\\Route\\Controller"))
            {
                $inst = $rc->newInstance($this->container);
            }
            else
            {
                $inst = $rc->newInstance();
            }

            $m = $rc->getMethod($callback[1]);
            $content = $m->invokeArgs($inst,$arg);
            return new Response($content);
        } elseif (is_callable($action[0])) {
            $rc = new \ReflectionFunction( $action[0] );
            $arg = $this->resolverParameter($rc,$request);
            $content = call_user_func_array($action[0], $arg);
            return new Response($content);
        }
        return new Response();
    }


    /**
     * @param Route $route
     * @return array
     */
    private function gatherRouteMiddleware(Route $route)
    {
        $action = $route->getOptions();

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