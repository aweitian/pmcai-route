<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/28
 * Time: 11:38
 */

namespace Aw\Routing\Route;

use Aw\Routing\Map\Cmr2Ncm as NcmMap;
use Aw\Routing\Matcher\IMatcher;

class Ncm extends Route
{
    public function __construct(IMatcher $matcher, NcmMap $map)
    {
        $this->matcher = $matcher;
        $this->map = $map;
        $this->newHook();
        $this->hook->addBeforeMapHook(function () {
            $this->map->map();
        });
        $this->hook->addBeforeDispatcherHook(function () {
            $this->dispatcher = new \Aw\Routing\Dispatch\Ncm($this->map);
        });
    }
}