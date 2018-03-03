<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:32
 * namespace优先级问题
 * namespace_map中的优先级比namespace的优先级高
 */

namespace Aw\Routing\Dispatch;

use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Parse\Pmcai;
use ReflectionClass;

class PmcaiDispatcher implements IDispatcher
{
    const DEFAULT_MODULE = 'def';
    const DEFAULT_CONTROL = 'main';
    const DEFAULT_ACTION = 'index';
    const DEFAULT_CONTROL_TPL = '{}Control';
    const DEFAULT_ACTION_TPL = '{}Action';
    const DEFAULT_CONTROL_NAMESPACE = "\\App\\Controller\\";
    public $logs = array();
    protected $namespace;
    protected $namespace_map = array();
    protected $ctl_tpl = self::DEFAULT_CONTROL_TPL;
    protected $act_tpl = self::DEFAULT_ACTION_TPL;

    /**
     * namespace|namespace_map|ctl_tpl|act_tpl
     * PmcaiDispatcher constructor.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $attrs = 'namespace|namespace_map|ctl_tpl|act_tpl';
        foreach (explode('|', $attrs) as $attr) {
            if (array_key_exists($attr, $data)) {
                $this->{$attr} = $data[$attr];
            }
        }
    }

    /**
     * @param Request $request
     * @return \Aw\Http\Response
     */
    public function dispatch(Request $request)
    {
        if (isset($request->carry['matcher'])) {
            $matcher = $request->carry['matcher'];
            if ($matcher instanceof Pmcai) {
                $module = $matcher->getModule();
                $ctl = $matcher->getControl();
                $act = $matcher->getAction();
                $ctl_tpl = $this->ctl_tpl;
                $act_tpl = $this->act_tpl;
                $namespace = $this->namespace;
                $namespace_map = $this->namespace_map;
                $data = compact('module', 'ctl', 'ctl_tpl', 'act', 'act_tpl', 'namespace', 'namespace_map');
                $ctl_class = self::getCtl($data);
                if (!$ctl_class || !class_exists($ctl_class)) {
                    $this->logs[] = "$ctl_class not found";
                    return new Response("$ctl_class not found", 500);
                }
                $rc = new ReflectionClass($ctl_class);
                $method = self::getAct($data);
                if (!$rc->hasMethod($method)) {
                    $this->logs[] = "Method $method not found";
                    return new Response("Method $method not found", 500);
                }
                $method_ins = $rc->getMethod($method);
                $ret = $method_ins->invoke($rc->newInstance(), $request);
                if ($ret instanceof Response) {
                    return $ret;
                } else {
                    return new Response($ret);
                }
            } else {
                $this->logs[] = "request is not via pmcai parser";
            }

        } else {
            $this->logs[] = "request is not via match";
        }
        return new Response('lost in the fog', 500);
    }

    /**
     * 按照参数检查方法是否存在
     * @param array $data namespace|ctl_tpl|act_tpl|module|ctl|act|namespace_map
     * @return bool
     */
    public static function isDispatchable(array $data = array())
    {
        $class = self::getCtl($data);
        if (!$class || !class_exists($class))
            return false;
        $rc = new ReflectionClass($class);
        return $rc->hasMethod(self::getAct($data));
    }

    protected static function getCtl($data)
    {
        $ns = self::getNamespace($data);
        if (!$ns)
            return false;
        if (isset($data['ctl_tpl'])) {
            $ctl_tpl = $data['ctl_tpl'];
        } else {
            $ctl_tpl = PmcaiDispatcher::DEFAULT_CONTROL_TPL;
        }
        if (isset($data['ctl']) && $data['ctl']) {
            $ctl = $data['ctl'];
        } else {
            $ctl = PmcaiDispatcher::DEFAULT_CONTROL;
        }
        return $ns . str_replace('{}', $ctl, $ctl_tpl);
    }

    protected static function getNamespace($data)
    {
        $mod = self::getModule($data);
        if (isset($data['namespace_map']) && isset($data['namespace_map'][$mod])) {
            return $data['namespace_map'][$mod];
        }
        if (isset($data['namespace']) && $data['namespace']) {
            return $data['namespace'];
        }
        return self::DEFAULT_CONTROL_NAMESPACE;
    }

    protected static function getModule($data)
    {
        if (isset($data['module'])) {
            $mod = $data['module'];
        } else {
            $mod = PmcaiDispatcher::DEFAULT_MODULE;
        }
        if ($mod)
            return $mod;
        return PmcaiDispatcher::DEFAULT_MODULE;
    }

    protected static function getAct($data)
    {
        if (isset($data['act_tpl'])) {
            $act_tpl = $data['act_tpl'];
        } else {
            $act_tpl = PmcaiDispatcher::DEFAULT_ACTION_TPL;
        }
        if (isset($data['act']) && $data['act']) {
            $act = $data['act'];
        } else {
            $act = PmcaiDispatcher::DEFAULT_ACTION;
        }
        return str_replace('{}', $act, $act_tpl);
    }
}