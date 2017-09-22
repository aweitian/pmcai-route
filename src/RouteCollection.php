<?php

/**
 *
 * @date 2017/7/1 08:54:58
 * 处理路由表
 *
 */

namespace Tian\Route;

use Closure;
use \Tian\Container;
use \Tian\Http\Request;
use \Tian\Http\Response;
use \Tian\Http\RequestContext;
use \Tian\Route\Matcher\UrlMatcher;
use \Tian\Route\Exception\MethodNotAllowedException;
use \Tian\Route\Exception\ResourceNotFoundException;

class RouteCollection implements \IteratorAggregate, \Countable
{
    protected $routes = [];
    /**
     * @var array
     */
    private $resources = array();
    /**
     * @var Request
     */
    private $currentRequest;

    /**
     * Indicates if filters should be run.
     *
     * @var bool
     */
    protected $runFilters = true;

    /**
     * @var Container;
     */
    protected $container;

    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
        }
    }


    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
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
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator|Route[] An \ArrayIterator object for iterating over routes
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Gets the number of Routes in this collection.
     *
     * @return int The number of routes
     */
    public function count()
    {
        return count($this->routes);
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
     * Adds a route.
     *
     * @param string $name The route name
     * @param Route $route A Route instance
     */
    public function add($name, Route $route)
    {
        unset($this->routes[$name]);

        $this->routes[$name] = $route;
    }

    /**
     * Adds a route collection at the end of the current set by appending all
     * routes of the added collection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     */
    public function addCollection(RouteCollection $collection)
    {
        // we need to remove all routes with the same names first because just replacing them
        // would not place the new route at the end of the merged array
        foreach ($collection->all() as $name => $route) {
            unset($this->routes[$name]);
            $this->routes[$name] = $route;
        }

        $this->resources = array_merge($this->resources, $collection->getResources());
    }

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return array of resources
     */
    public function getResources()
    {
        return array_unique($this->resources);
    }


    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function get($pattern, $action)
    {
        return $this->createRoute('get', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function post($pattern, $action)
    {
        return $this->createRoute('post', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function put($pattern, $action)
    {
        return $this->createRoute('put', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function patch($pattern, $action)
    {
        return $this->createRoute('patch', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function delete($pattern, $action)
    {
        return $this->createRoute('delete', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function options($pattern, $action)
    {
        return $this->createRoute('options', $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $method
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function match($method, $pattern, $action)
    {
        return $this->createRoute($method, $pattern, $action);
    }

    /**
     * Add a new route to the collection.
     *
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    public function any($pattern, $action)
    {
        return $this->createRoute('get|post|put|patch|delete', $pattern, $action);
    }

    /**
     * Get the response for a given request.
     *
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request)
    {
        $this->currentRequest = $request;

        // First we will call the "before" global middlware, which we'll give a chance
        // to override the normal requests process when a response is returned by a
        // middleware. Otherwise we'll call the route just like a normal request.
        $route = $this->findRoute($request);

        $response = $this->run($request, $route);

        return $response;
    }

    /**
     * Execute the route and return the response.
     *
     * @param  Request $request
     * @param  Route $route
     * @return Response
     */
    protected function run(Request $request, Route $route)
    {
        if (is_null($this->container))
        {
            $this->container = new Container();
        }
        $resolver = new ControllerResolver($this->container);
        return $resolver->resolver($request, $route);
    }

    /**
     * Match the given request to a route object.
     *
     * @param  Request $request
     * @return Route
     */
    protected function findRoute(Request $request)
    {
        // We will catch any exceptions thrown during routing and convert it to a
        // HTTP Kernel equivalent exception, since that is a more generic type
        // that's used by the Illuminate foundation framework for responses.
        try {
            $path = $request->getPathInfo();

            $parameters = $this->getUrlMatcher($request)->match($path);
        }

            // The Symfony routing component's exceptions implement this interface we
            // can type-hint it to make sure we're only providing special handling
            // for those exceptions, and not other random exceptions that occur.
        catch (MethodNotAllowedException $e) {
            $this->handleRoutingException($e);
        } catch (ResourceNotFoundException $e) {
            $this->handleRoutingException($e);
        }
        //var_dump($parameters['_route'],array_keys($this->routes));
        $route = $this->routes[$parameters['_route']];

        // If we found a route, we will grab the actual route objects out of this
        // route collection and set the matching parameters on the instance so
        // we will easily access them later if the route action is executed.
        $request->attributes->add($parameters);

        return $route;
    }

    /**
     * Convert routing exception to HttpKernel version.
     *
     * @param \Exception $e
     * @return void
     * @throws \Exception
     */
    protected function handleRoutingException(\Exception $e)
    {
        throw $e;
    }

    /**
     * Create a new URL matcher instance.
     *
     * @param  Request $request
     * @return UrlMatcher
     */
    protected function getUrlMatcher(Request $request)
    {
        $context = new RequestContext;

        $context->fromRequest($request);

        return new UrlMatcher($this, $context);
    }
//
//    /**
//     * Prepare the given value as a Response object.
//     *
//     * @param  mixed  $value
//     * @param  Request  $request
//     * @return Response
//     */
//    public function prepare($value, Request $request)
//    {
//        if ( ! $value instanceof Response) $value = new Response($value);
//
//        return $value->prepare($request);
//    }
//
//    /**
//     * Call a given global filter with the parameters.
//     *
//     * @param  Request  $request
//     * @param  string  $name
//     * @param  array   $parameters
//     * @return mixed
//     */
//    protected function callGlobalFilter(Request $request, $name, array $parameters = array())
//    {
//        if ( ! $this->filtersEnabled()) return;
//
//        array_unshift($parameters, $request);
//
//        if (isset($this->globalFilters[$name]))
//        {
//            // There may be multiple handlers registered for a global middleware so we
//            // will need to spin through each one and execute each of them and will
//            // return back first non-null responses we come across from a filter.
//            foreach ($this->globalFilters[$name] as $filter)
//            {
//                $response = call_user_func_array($filter, $parameters);
//
//                if ( ! is_null($response)) return $response;
//            }
//        }
//    }
//    /**
//     * Determine if route filters are enabled.
//     *
//     * @return bool
//     */
//    public function filtersEnabled()
//    {
//        return $this->runFilters;
//    }
//
//    /**
//     * Enable the running of filters.
//     *
//     * @return void
//     */
//    public function enableFilters()
//    {
//        $this->runFilters = true;
//    }
//
//    /**
//     * Disable the running of all filters.
//     *
//     * @return void
//     */
//    public function disableFilters()
//    {
//        $this->runFilters = false;
//    }
    /**
     * Create a new route instance.
     *
     * @param  string $method
     * @param  string $pattern
     * @param  mixed $action
     * @return \Tian\Route\Route
     */
    protected function createRoute($method, $pattern, $action)
    {
        // We will force the action parameters to be an array just for convenience.
        // This will let us examine it for other attributes like middlewares or
        // a specific HTTP schemes the route only responds to, such as HTTPS.
        if (!is_array($action)) {
            $action = $this->parseAction($action);
        }


        // Next we will parse the pattern and add any specified prefix to the it so
        // a common URI prefix may be specified for a group of routes easily and
        // without having to specify them all for every route that is defined.
        list($pattern, $optional) = $this->getOptional($pattern);

        if (isset($action['prefix'])) {
            $prefix = $action['prefix'];

            $pattern = $this->addPrefix($pattern, $prefix);
        }

        // We will create the routes, setting the Closure callbacks on the instance
        // so we can easily access it later. If there are other parameters on a
        // routes we'll also set those requirements as well such as defaults.
        $route = new Route($pattern);
        $route->setOptions(array(
            '_call' => $action,
        ));
        //->addRequirements($pattern);

        $route->setRequirement('_method', $method);

        // Once we have created the route, we will add them to our route collection
        // which contains all the other routes and is used to match on incoming
        // URL and their appropriate route destination and on URL generation.
        $this->setAttributes($route, $action, $optional);

        $name = $this->getName($method, $pattern, $action);

        $this->routes[$name] = $route;

        return $route;
    }

    /**
     * Parse the given route action into array form.
     *
     * @param  mixed $action
     * @return array
     */
    protected function parseAction($action)
    {
        // If the action is just a Closure we'll stick it in an array and just send
        // it back out. However if it's a string we'll just assume it's meant to
        // route into a controller action and change it to a controller array.
        if ($action instanceof Closure) {
            return array($action);
        } elseif (is_string($action)) {
            return array('uses' => $action);
        }

        throw new \InvalidArgumentException("Unroutable action.");
    }

    /**
     * Modify the pattern and extract optional parameters.
     *
     * @param  string $pattern
     * @return array
     */
    protected function getOptional($pattern)
    {
        $optional = array();

        preg_match_all('#\{(\w+)\?\}#', $pattern, $matches);

        // For each matching value, we will extract the name of the optional values
        // and add it to our array, then we will replace the place-holder to be
        // a valid place-holder minus this optional indicating question mark.
        foreach ($matches[0] as $key => $value) {
            $optional[] = $name = $matches[1][$key];

            $pattern = str_replace($value, '{' . $name . '}', $pattern);
        }

        return array($pattern, $optional);
    }

    /**
     * Add the given prefix to the given URI pattern.
     *
     * @param  string $pattern
     * @param  string $prefix
     * @return string
     */
    protected function addPrefix($pattern, $prefix)
    {
        $pattern = trim($prefix, '/') . '/' . ltrim($pattern, '/');

        return trim($pattern, '/');
    }

    /**
     * Get the name of the route.
     *
     * @param  string $method
     * @param  string $pattern
     * @param  array $action
     * @return string
     */
    protected function getName($method, $pattern, array $action)
    {
        if (isset($action['as'])) return $action['as'];

        $domain = isset($action['domain']) ? $action['domain'] . ' ' : '';

        return "{$domain}{$method} {$pattern}";
    }

    /**
     * Set the attributes and requirements on the route.
     *
     * @param  \Tian\Route\Route $route
     * @param  array $action
     * @param  array $optional
     * @return void
     */
    protected function setAttributes(Route $route, $action, $optional)
    {
        // First we will set the requirement for the HTTP schemes. Some routes may
        // only respond to requests using the HTTPS scheme, while others might
        // respond to all, regardless of the scheme, so we'll set that here.
        if (in_array('https', $action)) {
            $route->setRequirement('_scheme', 'https');
        }

        if (in_array('http', $action)) {
            $route->setRequirement('_scheme', 'http');
        }

        // If there is a "uses" key on the route it means it is using a controller
        // instead of a Closures route. So, we'll need to set that as an option
        // on the route so we can easily do reverse routing ot the route URI.
        if (isset($action['uses'])) {
            $route->setOption('_uses', $action['uses']);
        }

        if (isset($action['domain'])) {
            $route->setHost($action['domain']);
        }

        // Finally we will go through and set all of the default variables to null
        // so the developer doesn't have to manually specify one each time they
        // are declared on a route. This is simply for developer convenience.
        foreach ($optional as $key) {
            $route->setDefault($key, null);
        }
    }
}