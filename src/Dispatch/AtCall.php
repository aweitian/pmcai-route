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
    public function __construct($call, $namespace = "\\App\\Controller\\")
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

        $rc = new \ReflectionClass($cls);
        if (!$rc->hasMethod($act)) {
            $this->response = new Response("{$act} in {$cls} is not found", 404);
            return false;
        }
        $method = $rc->getMethod($cls);
        if (!$method->isPublic()) {
            $this->response = new Response("{$act} in {$cls} should be public", 403);
            return false;
        }
        $inst = $rc->newInstance($request);
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
            return $this->namespace . $cls;
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