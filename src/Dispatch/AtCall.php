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
    protected $response;

    /**
     * @param mixed $call
     * @param string $namespace
     */
    public function __construct($call, $namespace = "\\App\\Controller")
    {
        $this->callback = $call;
        $this->namespace = $namespace;
    }


    /**
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function dispatch(Request $request)
    {
        $arr = explode("@", $this->callback);
        if (count($arr) > 1) {
            $cls = $this->handleNamespace($arr[0]);
            $act = $arr[1];
        } else {
            $cls = $this->handleNamespace($this->callback);
            $act = self::DEFAULT_ACTION;
        }
        if (!class_exists($cls)) {
            $this->response = new Response("class {$cls} is not found", 404);
            return false;
        }
        $rc = new \ReflectionClass($cls);
        if (!$rc->hasMethod($act)) {
            $this->response = new Response("{$act} in {$cls} is not found", 404);
            return false;
        }
        $method = $rc->getMethod($act);
        if (!$method->isPublic()) {
            $this->response = new Response("{$act} in {$cls} should be public", 403);
            return false;
        }

        if ($rc->hasMethod("__construct")) {
            $inst = $rc->newInstance($request);
        } else {
            $inst = $rc->newInstance();
        }

        $ret = $method->invoke($inst);
        if ($ret instanceof Response) {
            $this->response = $ret;
        } else {
            $this->response = new Response($ret);
        }
        return true;
    }

    protected function handleNamespace($cls)
    {
        if (substr($cls, 0, 1) == "\\") {
            return $cls;
        } else {
            return rtrim($this->namespace, '\\') . '\\' . $cls;
        }
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}