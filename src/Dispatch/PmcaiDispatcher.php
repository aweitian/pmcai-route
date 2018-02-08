<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:32
 */

namespace Aw\Routing\Dispatch;
class PmcaiDispatcher
{
    const DEFAULT_CONTROL = 'main';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_CONTROL_TPL = '{}Control';
    const DEFAULT_ACTION_TPL = '{}Action';
    protected $namespace;
    protected $loc_map = array();
    protected $namespace_map = array();
    protected $ctrl_tpl = '{}Controller';
    protected $act_tpl = '{}Action';

    /**
     * @param array $data namespace(必须有)|ctl_loc|ctl_tpl|act_tpl|ctl|act
     * @return bool
     */
    public static function isDispatchable(array $data = array())
    {
        if (!isset($data['namespace']))
            return false;
        $ns = $data['namespace'];
        if (isset($data['ctl_loc']) && file_exists($data['ctl_loc']))
            require_once $data['ctl_loc'];
        if (isset($data['ctl_tpl'])) {
            $ctl_tpl = $data['ctl_tpl'];
        } else {
            $ctl_tpl = PmcaiDispatcher::DEFAULT_CONTROL_TPL;
        }
        if (isset($data['act_tpl'])) {
            $act_tpl = $data['act_tpl'];
        } else {
            $act_tpl = PmcaiDispatcher::DEFAULT_ACTION_TPL;
        }

        if (isset($data['ctl'])) {
            $ctl = $data['ctl'];
        } else {
            $ctl = PmcaiDispatcher::DEFAULT_CONTROL;
        }
        if (isset($data['act'])) {
            $act = $data['act'];
        } else {
            $act = PmcaiDispatcher::DEFAULT_ACTION;
        }
        $class = $ns . str_replace('{}', $ctl, $ctl_tpl);
        if (!class_exists($class))
            return false;
        $rc = new \ReflectionClass($class);
        return $rc->hasMethod(str_replace('{}', $act, $act_tpl));
    }
}