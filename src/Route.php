<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 11:01
 * Route 是一条路由记录
 *      一条路由记录由3个属性组成
 *      - 匹配URL参数,把这个参数用UrlMatcher匹配
 *      - 中间件,匹配成功后需要经过的管道
 *      - 通过管道后执行的业务函数
 */

namespace Aw\Routing;

use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Pipeline;
use Aw\Routing\Dispatch\IDispatcher;
use Aw\Routing\Matcher\IMatcher;
use Aw\Routing\Router\Middleware;
use Aw\Routing\Router\Router;


class Route
{

    /**
     * @var IMatcher $matches
     */
    protected $matcher;         //用于匹配的数据
    /**
     * @var Middleware
     */
    protected $middleware;    //匹配成功需要通过的中间件
    /**
     * @var IDispatcher
     */
    protected $dispatcher;        //通过中间件后执行的业务逻辑
    /**
     * @var Request
     */
    protected $request;
    protected $action;
    protected $privateMiddleware = array();


    public $logs = array();
    public $useGlobalMiddleware = true;

    /**
     * @var Router
     */
    protected $router = null;
    protected $name;

    public function __construct(IMatcher $matcher = null, Middleware $middleware = null, $private_middleware = array(), $action = null, $useGlobalMiddleware = true)
    {
        $this->useGlobalMiddleware = $useGlobalMiddleware;
        $this->matcher = $matcher;
        $this->action = $action;
        $this->privateMiddleware = $private_middleware;
        $this->middleware = $middleware;
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * 此函数   不会   改变路由表顺序
     * @param $name
     * @param bool $from
     */
    public function setName($name, $from = null)
    {
        if (!is_null($this->router)) {
            if (!($from instanceof Router)) {
                $this->router->onRouteNameChange($this, $name);
            }
        }
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        if (is_null($this->matcher)) {
            $this->logs[] = "matcher is null";
            return false;
        }
        if ($this->matcher->match($request)) {
            $this->request = $request;
            return true;
        }
        return false;
    }

    /**
     * @return Response
     */
    public function route()
    {
        if ($this->request == null) {
            $this->logs[] = "request is null";
            return new Response('request is null', 500);
        }
        if ($this->dispatcher == null) {
            $this->logs[] = "dispatcher is null";
            return new Response('dispatcher is null', 500);
        }
        if ($this->middleware instanceof Middleware) {
            $mw = $this->middleware->getMiddleware($this->privateMiddleware, $this->useGlobalMiddleware);
        } else {
            $mw = array();
        }
        $dp = $this->dispatcher;
        $pipe = new Pipeline();
        return $pipe->send($this->request)
            ->through($mw)
            ->then(function ($request) use ($dp) {
                return $dp->dispatch($request);
            });
    }

    /**
     * @return IMatcher
     */
    public function getMatch()
    {
        return $this->matcher;
    }

    /**
     * @param IMatcher $match
     */
    public function setMatch(IMatcher $match)
    {
        $this->matcher = $match;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param mixed $middleware
     */
    public function setMiddleware($middleware)
    {
        $this->middleware = $middleware;
    }


    /**
     * @return IDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param IDispatcher $dispatcher
     */
    public function setDispatcher(IDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return Route
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrivateMiddleware()
    {
        return $this->privateMiddleware;
    }

    /**
     * @param array $privateMiddleware
     * @return Route
     */
    public function setPrivateMiddleware($privateMiddleware)
    {
        $this->privateMiddleware = $privateMiddleware;
        return $this;
    }
}