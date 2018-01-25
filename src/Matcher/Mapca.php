<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Mapca implements IRequestMatcher
{
    protected $prefix;
    protected $mask;
    protected $loc_map;
    protected $namespace_map;
    protected $ctrl_tpl;
    protected $act_tpl;

    /**
     * URL格式 为 {prefix}{m}{c}{a}
     * Mapca constructor.
     * @param string $prefix
     * @param string $mask
     * @param array $loc_map
     * @param array $namespace_map
     * @param string $ctrl_tpl
     * @param string $act_tpl
     */
    public function __construct($prefix = "", $mask = "ca", array $loc_map = array(), array $namespace_map = array(), $ctrl_tpl = '{}Controller', $act_tpl = '{}Action')
    {
        $this->prefix = $prefix;
        $this->mask = $mask;
        $this->loc_map = $loc_map;
        $this->namespace_map = $namespace_map;
        $this->ctrl_tpl = $ctrl_tpl;
        $this->act_tpl = $act_tpl;
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $url = $request->getPath();
        return !!preg_match($this->regexp, $url, $this->matches);
    }
}