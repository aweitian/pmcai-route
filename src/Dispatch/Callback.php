<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Dispatch;


use Aw\Http\Request;
use Aw\Http\Response;

class Callback implements IDispatcher
{
    protected $callback;
    public $logs = array();
    /**
     * @param mixed $call
     */
    public function __construct($call)
    {
        $this->callback = $call;
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function dispatch(Request $request)
    {
        $callback = $this->callback;
        if (!is_callable($callback))
            throw new \Exception('callback is not callable');
        return $callback($request);
    }
}