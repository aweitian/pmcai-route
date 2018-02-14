<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/13
 * Time: 9:32
 */

namespace Aw\Routing\Router;


use Aw\Routing\Dispatch\AtCall;
use Aw\Routing\Dispatch\Callback;
use Aw\Routing\Dispatch\IDispatcher;
use Aw\Routing\Dispatch\PmcaiDispatcher;

class DispatcherFactory
{
    /**
     * @param $action
     * @return IDispatcher
     */
    public static function CreateByAction($action)
    {
        if ($action instanceof \Closure) {
            return new Callback($action);
        } else if (is_array($action)) {
            return new PmcaiDispatcher($action);
        } else if (is_string($action)) {
            return new AtCall($action);
        }
    }
}