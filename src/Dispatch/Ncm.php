<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 9:53
 * namespace class method
 * 就是一个简单的反射调用
 */

namespace Aw\Routing\Dispatch;


use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Map\INcm;

class Ncm implements IDispatcher
{
    protected $response;
    protected $ncm;
    protected $namespace;
    protected $class;
    protected $method;

    /**
     * Ncm constructor.
     * @param INcm $ncm
     */
    public function __construct(INcm $ncm)
    {
        $this->ncm = $ncm;
    }


    /**
     * @param Request $request
     * @return Response|bool|mixed
     */
    public function dispatch(Request $request)
    {
        $namespace = $this->ncm->getNamespace();
        $namespace = rtrim($namespace, '\\');
        $full_class = $namespace . '\\' . $this->ncm->getClass();
        if (!class_exists($full_class)) {
            $this->response = new Response("class {$full_class} is not found", 404);
            return false;
        }
        $rc = new \ReflectionClass($full_class);
        if (!$rc->hasMethod($this->ncm->getMethod())) {
            $this->response = new Response("{$this->ncm->getMethod()} in {$full_class} is not found", 404);
            return false;
        }
        $method = $rc->getMethod($this->ncm->getMethod());
        if (!$method->isPublic()) {
            $this->response = new Response("{$this->ncm->getMethod()}  in {$full_class} should be public", 403);
            return false;
        }
        $inst = $rc->newInstance($request);
        $ret = $method->invoke($inst, $this->ncm);
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