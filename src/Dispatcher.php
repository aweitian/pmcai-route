<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:32
 */
namespace Aw\Routing;
use Closure;

class Dispatcher
{
    const DEFAULT_CONTROL = 'main';
    const DEFAULT_ACTION  = 'index';
    const DEFAULT_CONTROL_TPL = '{}Control';
    const DEFAULT_ACTION_TPL = '{}Action';
    protected $namespace;
    protected $loc_map = array();
    protected $namespace_map = array();
    protected $ctrl_tpl = '{}Controller';
    protected $act_tpl = '{}Action';

    public function __construct(array $action = array())
    {
    }

    /**
     * @return Closure
     */
    public function getAction()
    {
        return function (){};
    }
}