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

class Mapca implements IMatcher
{
    const TYPE_PMCAI = 'pmcai';
    const TYPE_PMI = 'pmi';
    const TYPE_ARR = 'arr';
    /**
     * @var mixed
     */
    public $matcher;
    protected $prefix = '';
    protected $mask = 'ca';
    protected $type = self::TYPE_PMCAI;
    protected $moduleSkip = true;
    protected $module = null;

    /**
     * URL格式 为 {prefix}{m}{c}{a}
     * Mapca constructor.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $attrs = 'prefix|mask|moduleSkip|type|module';
        foreach (explode('|', $attrs) as $attr) {
            if (array_key_exists($attr, $data)) {
                $this->{$attr} = $data[$attr];
            }
        }
    }


    /**
     * 匹配成功设置request->carry['matcher']
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $url = $request->getPath();
        switch ($this->type) {
            case self::TYPE_PMCAI:
                $this->matcher = new Pmcai(array(
                    'http_entry' => $this->prefix,
                    'mask' => $this->mask
                ));
                if ($this->matcher->parse($url)) {
                    if (!is_null($this->module)) {
                        if ($this->matcher->getModule() != $this->module) {
                            break;
                        }
                    }
                    $request->carry['matcher'] = $this->matcher;
                    return true;
                };
                break;
            case self::TYPE_PMI:
                $this->matcher = new Pmi();
                if ($this->moduleSkip) {
                    $this->matcher->setModuleSkipOn();
                } else {
                    $this->matcher->setModuleSkipOff();
                }
                if ($this->matcher->parse($url)) {
                    if (!$this->moduleSkip && !is_null($this->module)) {
                        if ($this->matcher->getModule() != $this->module) {
                            break;
                        }
                    }
                    $request->carry['matcher'] = $this->matcher;
                    return true;
                };
                break;
            case self::TYPE_ARR:
                $this->matcher = new Arr();
                if ($this->matcher->parse($url)) {
                    $request->carry['matcher'] = $this->matcher;
                    return true;
                };
                break;
        }
        return false;
    }
}