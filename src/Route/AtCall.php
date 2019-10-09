<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 11:38
 */

namespace Aw\Routing\Route;

use Aw\Routing\Matcher\IMatcher;

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
        $this->hook->addBeforeDispatcherHook(function () {
            if (is_string($this->at_call_ns)) {
                $this->dispatcher = new \Aw\Routing\Dispatch\AtCall($this->at_call, $this->at_call_ns);
            } else {
                $this->dispatcher = new \Aw\Routing\Dispatch\AtCall($this->at_call);
            }
        });
    }
}