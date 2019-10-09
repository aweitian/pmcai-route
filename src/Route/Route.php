<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/8
 * Time: 11:29
 */

namespace Aw\Routing\Route;


use Aw\Http\Request;
use Aw\Pipeline;
use Aw\Routing\Dispatch\IDispatcher;
use Aw\Routing\Map\INcm;
use Aw\Routing\Matcher\IMatcher;

abstract class Route implements IRoute
{
    /**
     * @var IMatcher
     */
    public $matcher;
    /**
     * @var INcm
     */
    public $map;
    /**
     * @var IDispatcher
     */
    public $dispatcher;
    protected $result;

    /**
     * @var RouteHook
     */
    public $hook;

    public function newHook()
    {
        $this->hook = new RouteHook();
    }

    public function getDispatchResult()
    {
        return $this->result;
    }

    /**
     * @param Request $request
     * @param array $middleware
     * @return bool
     */
    public function route(Request $request, array $middleware)
    {
        $this->beforeMatch($request);
        if (!$this->matcher->match($request))
            return false;
        $this->beforeMap($this->matcher, $request);
        $this->beforeDispatcher($this->map, $request);
        $that = $this;
        $pipe = new Pipeline();
        return $pipe->send($request)
            ->through($middleware)
            ->then(function ($request) use ($that) {
                $f = $that->dispatcher->dispatch($request);
                $that->result = $that->dispatcher->getResponse();
                return $f;
            });
    }

    public function beforeMatch(Request $request)
    {
        if (!($this->hook instanceof RouteHook))
            return;
        foreach ($this->hook->getBeforeMatcherHook() as $callback) {
            $callback($request);
        }
    }

    public function beforeMap(IMatcher $matcher, Request $request)
    {
        if (!($this->hook instanceof RouteHook))
            return;
        foreach ($this->hook->getBeforeMapHook() as $callback) {
            $callback($matcher, $request);
        }
    }

    public function beforeDispatcher(INcm $map, Request $request)
    {
        if (!($this->hook instanceof RouteHook))
            return;
        foreach ($this->hook->getBeforeDispatcherHook() as $callback) {
            $callback($map, $request);
        }
    }

    public function getRouteHook()
    {
        return $this->hook;
    }
}