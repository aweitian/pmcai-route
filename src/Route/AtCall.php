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
use Aw\Routing\Dispatch\AtCall as AtCallDispatcher;
use Aw\Routing\Matcher\Group;
use Aw\Routing\Matcher\IMatcher;
use Aw\Routing\Matcher\OrGroup;
use Aw\Routing\Matcher\Regexp;
use Exception;

class AtCall extends Route
{
    protected $at_call_ns;
    protected $at_call;

    public function __construct(IMatcher $matcher, $at_call, $at_call_ns = null)
    {
        $this->matcher = $matcher;
        $this->at_call_ns = $at_call_ns;
        $this->at_call = $at_call;
        $this->newHook();
    }

    /**
     * (:1|def)
     * 处理action上的占位符,默认值
     */
    protected function preFilterCallback()
    {
        if (is_string($this->at_call_ns)) {
            $this->dispatcher = new AtCallDispatcher($this->at_call, $this->at_call_ns);
        } else {
            $this->dispatcher = new AtCallDispatcher($this->at_call);
        }
        $f1 = ($this->matcher instanceof OrGroup || $this->matcher instanceof Group) && $this->matcher->hasUrlMatcher && $this->matcher->getUrlMatcher() instanceof Regexp;
        $f2 = $this->matcher instanceof Regexp;

        if ($f2) {
            $matcher = $this->matcher;
        } else if ($f1) {
            $matcher = $this->matcher->getUrlMatcher();
        } else {
            return;
        }
        $trans = array();
        $result = $matcher->result;
        $c = preg_split("/[\\\@]/", $this->dispatcher->callback);
        foreach ($c as $item) {
            if (preg_match('#\(\:(\d+)(\|\w+)?\)#', $item, $m)) {
                //$trans[$item] = $m;
                if (!isset($result[$m[1]])) {
                    //下标索引不存在,必须得有默认值
                    if (!isset($m[2])) {
                        throw new Exception("$item 下标索引不存在,并且没有默认值");
                    } else {
                        $trans[$item] = $m[2];
                    }
                } else {
                    $trans[$item] = $result[$m[1]];
                }
            }
        }
        if (!empty($trans))
            $this->dispatcher->callback = strtr($this->dispatcher->callback, $trans);
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

        $this->preFilterCallback();
        $this->beforeDispatcher($this->matcher, $request);
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
}