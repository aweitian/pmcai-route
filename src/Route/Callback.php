<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 11:38
 */

namespace Aw\Routing\Route;

use Aw\Routing\Matcher\IMatcher;

class Callback extends Route
{
    protected $callback;

    public function __construct(IMatcher $matcher, \Closure $callback)
    {
        $this->matcher = $matcher;
        $this->callback = $callback;
        $this->newHook();
        $this->hook->addBeforeDispatcherHook(function () {
            $this->dispatcher = new \Aw\Routing\Dispatch\Callback($this->callback);
        });
    }
}