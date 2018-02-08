<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;
use Aw\Routing\Parse\Arr;
use Aw\Routing\Parse\Pmcai;
use Aw\Routing\Parse\Pmi;

class Mapca implements IRequestMatcher
{
    /**
     * @var mixed
     */
    public $matcher;
    protected $prefix = '';
    protected $mask = 'ca';
    protected $loc_map = array();
    protected $namespace_map = array();
    protected $ctrl_tpl = '{}Controller';
    protected $act_tpl = '{}Action';
    protected $type = 'pmcai';//pmi|arr|pmcai
    protected $moduleSkip = true;
    /**
     * URL格式 为 {prefix}{m}{c}{a}
     * Mapca constructor.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $attrs = 'prefix|mask|loc_map|namespace_map|ctrl_tpl|act_tpl|moduleSkip|type';
        foreach (explode('|', $attrs) as $attr) {
            if (array_key_exists($attr, $data)) {
                $this->{$attr} = $data[$attr];
            }
        }
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $url = $request->getPath();
        switch ($this->type)
        {
            case 'pmcai':
                $this->matcher = new Pmcai(array(
                    'http_entry' => $this->prefix,
                    'mask' => $this->mask
                ));
                return $this->matcher->parse($url);
            case 'pmi':
                $this->matcher = new Pmi();
                if ($this->moduleSkip)
                {
                    $this->matcher->setModuleSkipOn();
                }
                else
                {
                    $this->matcher->setModuleSkipOff();
                }
                return $this->matcher->parse($url);
            case 'arr':
                $this->matcher = new Arr();
                return $this->matcher->parse($url);
        }
        return false;
    }
}