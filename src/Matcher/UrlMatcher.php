<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tian\Route\Matcher;

use Tian\Route\Exception\MethodNotAllowedException;
use Tian\Route\Exception\ResourceNotFoundException;
use Tian\Route\RouteCollection;
use Tian\Http\Request;
use Tian\Route\Route;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UrlMatcher
{
    const REQUIREMENT_MATCH = 0;
    const REQUIREMENT_MISMATCH = 1;
    const ROUTE_MATCH = 2;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var array
     */
    protected $allow = array();
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes A RouteCollection instance
     * @param Request $request
     */
    public function __construct(RouteCollection $routes, Request $request = null)
    {
        $this->routes = $routes;
        if (!is_null($request)) {
            $this->setRequest($request);
        }
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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function match($request = null)
    {
        $pathinfo = "";
        if (!is_null($request))
        {
            if ($request instanceof Request)
            {
                $this->request = $request;
                $pathinfo = $request->getPathInfo();
            }
            else
            {
                $pathinfo = $request;
            }
        }else{
            if (!is_null($this->request))
            {
                $pathinfo = $this->request->getPathInfo();
            }
        }
        $this->allow = array();
        if ($ret = $this->matchCollection(rawurldecode($pathinfo), $this->routes)) {
            return $ret;
        }
        throw 0 < count($this->allow)
            ? new MethodNotAllowedException(array_unique(array_map('strtoupper', $this->allow)))
            : new ResourceNotFoundException();
    }

    /**
     * Tries to match a URL with a set of routes.
     *
     * @param string $pathinfo The path info to be parsed
     * @param RouteCollection $routes The set of routes
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
    protected function matchCollection($pathinfo, RouteCollection $routes)
    {
        /**
         * @var \Tian\Route\Route $route
         */
        foreach ($routes as $name => $route) {
            $compiledRoute = $route->compile();
            // check the static prefix of the URL first. Only use the more expensive preg_match when it matches
            if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($pathinfo, $compiledRoute->getStaticPrefix())) {
                continue;
            }
            if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
                continue;
            }
            $hostMatches = array();
            if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $this->request->getHost(), $hostMatches)) {
                continue;
            }
            // check HTTP method requirement
            if ($req = $route->getRequirement('_method')) {
                // HEAD and GET are equivalent as per RFC
                if ('HEAD' === $method = $this->request->getMethod()) {
                    $method = 'GET';
                }
                if (!in_array($method, $req = explode('|', strtoupper($req)))) {
                    $this->allow = array_merge($this->allow, $req);
                    continue;
                }
            }
            $status = $this->handleRouteRequirements($route);

            if (self::REQUIREMENT_MISMATCH === $status[0]) {
                continue;
            }
            return $this->getAttributes($route, $name, array_replace($matches, $hostMatches, isset($status[1]) ? $status[1] : array()));
        }
        return [];
    }

    /**
     * Returns an array of values to use as request attributes.
     *
     * As this method requires the Route object, it is not available
     * in matchers that do not have access to the matched Route instance
     * (like the PHP and Apache matcher dumpers).
     *
     * @param Route $route The route we are matching against
     * @param string $name The name of the route
     * @param array $attributes An array of attributes from the matcher
     *
     * @return array An array of parameters
     */
    protected function getAttributes(Route $route, $name, array $attributes)
    {
        $attributes['_route'] = $name;
        return $this->mergeDefaults($attributes, $route->getDefaults());
    }

    /**
     * Handles specific route requirements.
     *
     * @param Route $route The route
     *
     * @return array The first element represents the status, the second contains additional information
     */
    protected function handleRouteRequirements(Route $route)
    {
        // check HTTP scheme requirement
        $scheme = $route->getRequirement('_scheme');
        $status = $scheme && $scheme !== $this->request->getScheme() ? self::REQUIREMENT_MISMATCH : self::REQUIREMENT_MATCH;
        return array($status, null);
    }

    /**
     * Get merged default parameters.
     *
     * @param array $params The parameters
     * @param array $defaults The defaults
     *
     * @return array Merged default parameters
     */
    protected function mergeDefaults($params, $defaults)
    {
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $defaults[$key] = $value;
            }
        }
        return $defaults;
    }
}