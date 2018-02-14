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

class AtCall implements IDispatcher
{
    const DEFAULT_ACTION = "index";
    protected $callback;
    protected $namespace;
    public $logs = array();

    /**
     * @param mixed $call
     * @param string $namespace
     */
    public function __construct($call, $namespace = "\\App\\Controller\\")
    {
        $this->callback = $call;
        $this->namespace = $namespace;
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function dispatch(Request $request)
    {
        $arr = explode("@", $this->callback);
        if (count($arr) > 1) {
            $cls = $this->namespace . $arr[0];
            $act = $arr[1];
        } else {
            $cls = $this->namespace . $this->callback;
            $act = self::DEFAULT_ACTION;
        }
        if (!class_exists($cls)) {
            $this->logs[] = "class $cls does not exists";
            return new Response("class $cls does not exists", 500);
        }
        $cls_ins = new $cls();
        return $cls_ins->$act($request);
    }
}