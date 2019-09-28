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
    protected $response;

    /**
     * @param mixed $call
     */
    public function __construct($call)
    {
        $this->callback = $call;
    }


    public function dispatch(Request $request)
    {
        $callback = $this->callback;
        if (!is_callable($callback)) {
            return false;
        }
        $ret = $callback($request);
        if ($ret === false)
            return false;
        if ($ret instanceof Response) {
            $this->response = $ret;
        } else {
            $this->response = new Response($ret);
        }
        return true;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}