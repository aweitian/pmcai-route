<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 11:38
 */

namespace Aw\Routing\Route;


use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Pipeline;
use Aw\Routing\Map\Ncm as NcmMap;
use Aw\Routing\Matcher\IMatcher;

class Ncm implements IRoute
{
    public $matcher;
    public $map;
    /**
     * @var \Aw\Routing\Dispatch\Ncm
     */
    public $dispatcher;
    private $result;

    public function __construct(IMatcher $matcher, NcmMap $map)
    {
        $this->matcher = $matcher;
        $this->map = $map;
    }

    /**
     * @return Response
     */
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
        if (!$this->matcher->match($request))
            return false;
        $this->map->map();
        $this->dispatcher = new \Aw\Routing\Dispatch\Ncm($this->map);
        $that = $this;
        $pipe = new Pipeline();
        $pipe->send($request)
            ->through($middleware)
            ->then(function ($request) use ($that) {
                $f = $that->dispatcher->dispatch($request);
                $that->result = $that->dispatcher->getResponse();
                return $f;
            });
    }
}