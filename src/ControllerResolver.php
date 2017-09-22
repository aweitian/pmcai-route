<?php
/**
 * Ccontrol Action 存于Route的Option中
 * Created by PhpStorm.
 * User: awei.tian
 * Date: 9/20/17
 * Time: 8:03 PM
 */

namespace Tian\Route;
use \Closure;
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
    public function resolver(Request $request, Route $route)
    {
//        var_dump($request->attributes->all());exit;
        $call = $route->getOption("_call");
        if (array_key_exists("uses", $call)) {
            $callback = explode("@", $call['uses']);
            if ($callback[0][0] != "\\") {
                if ($ns = $route->getOption("_namespace")) {
                    $namespace = rtrim($ns, "\\") . "\\" . $callback[0];
                } else {
                    $namespace = $this->defNamespace . "\\" . $callback[0];
                }
            } else {
                $namespace = $callback[0];
            }
            //$callback = $namespace."::".$callback[1];
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

    protected function middleware(Route $route, Request $request)
    {
        $shouldSkipMiddleware = $this->container->bound('middleware.disable') &&
            $this->container->make('middleware.disable') === true;

        $middleware = $shouldSkipMiddleware ? [] : $this->gatherRouteMiddleware($route);

        return (new Pipeline())
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($route) {
                return $this->prepareResponse(
                    $request, $route->run()
                );
            });

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