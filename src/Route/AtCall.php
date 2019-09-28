<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 11:38
 */

namespace Aw\Routing\Route;


use Aw\Http\Request;
use Aw\Pipeline;
use Aw\Routing\Matcher\IMatcher;

class AtCall implements IRoute
{
    public $matcher;
    public $map;
    public $dispatcher;
    private $result;

    public function __construct(IMatcher $matcher, $at_call, $at_call_ns = null)
    {
        $this->matcher = $matcher;
        if (is_string($at_call_ns)) {
            $this->dispatcher = new \Aw\Routing\Dispatch\AtCall($at_call, $at_call_ns);
        } else {
            $this->dispatcher = new \Aw\Routing\Dispatch\AtCall($at_call);
        }
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
        if (!$this->matcher->match($request))
            return false;
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