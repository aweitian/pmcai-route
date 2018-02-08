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

class Router
{
    protected $routes = array();
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @return Route
     */
    public function get($pattern, $action, $middleware = array())
    {
        return $this->request('GET', $pattern, $action, $middleware);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @return Route
     */
    public function post($pattern, $action, $middleware = array())
    {
        return $this->request('POST', $pattern, $action, $middleware);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @return Route
     */
    public function put($pattern, $action, $middleware = array())
    {
        return $this->request('PUT', $pattern, $action, $middleware);
    }

    /**
     * @param $pattern
     * @param $action
     * @param array $middleware
     * @return Route
     */
    public function delete($pattern, $action, $middleware = array())
    {
        return $this->request('delete', $pattern, $action, $middleware);
    }

    /**
     * @param $config
     * @param $action
     * @param $middleware
     * @return Route
     */
    public function map($config, $action, $middleware)
    {
        $route = new Route();
        $route->setRequest($this->request);
        $route->setMatch($config);
        $route->setMiddleware($middleware);
        $route->setAction($action);
        $this->add($route, isset($config['name']) ? $config['name'] : null);
        return $route;
    }

    /**
     * @param $method
     * @param $pattern
     * @param $action
     * @param $middleware
     * @return Route
     */
    protected function request($method, $pattern, $action, $middleware)
    {
        return $this->map(array(
            'regexp' => $pattern,
            'method' => $method
        ), $action, $middleware);
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
            if ($route->match()) {
                $response = $route->route();
                if ($response instanceof Response) {
                    return $response;
                }
                return new Response($response);
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