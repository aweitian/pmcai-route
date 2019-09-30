<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/26
 * Time: 17:54
 * 类简单,开放所有方法
 */

namespace Aw\Routing\Map;

use Aw\Routing\Matcher\Ca;
use Aw\Routing\Matcher\Mca;
use Aw\Routing\Matcher\Regexp;

class Ncm implements INcm
{
    const DIGITAL_PREFIX = '_';//module control action 到 namespace class method映射时,如果是数字开头,加上这个前缀
    protected $matcher_which = '';
    public $matcher;

    public $class_pattern;
    public $method_pattern;
    public $namespace_pattern;
    public $c_default;
    public $a_default;
    public $m_default;


    public $raw_module;
    public $raw_control;
    public $raw_action;

    public $module;
    public $control;
    public $action;
    public $namespace;
    public $class;
    public $method;

    public function __construct($matcher, $class_pattern = '{c}', $method_pattern = '{a}', $namespace_pattern = 'App\Modules\{m}', $c_default = 'main', $a_default = 'index', $m_default = 'Controller')
    {
        if ($matcher instanceof Ca) {
            $this->matcher_which = 'ca';
        } else if ($matcher instanceof Mca) {
            $this->matcher_which = 'mca';
        } else if ($matcher instanceof Regexp) {
            $this->matcher_which = 'regexp';
        } else {
            throw new \Exception("invalid matcher");
        }
        $this->matcher = $matcher;
        $this->class_pattern = $class_pattern;
        $this->method_pattern = $method_pattern;
        $this->namespace_pattern = $namespace_pattern;
        $this->c_default = $c_default;
        $this->a_default = $a_default;
        $this->m_default = $m_default;
    }

    /**
     * mca
     * 正常情况下只调用这一个方法即可
     * 如果长度大于等于3 存在 0 作为 {m} ,1 作为 {c},2作为{a}
     * 如果长度小于3 , 存在0 作为 {c} ,存在1 作为 {a}
     */
    public function map()
    {
        $this->url2raw();//url to raw => map
        $this->raw2mca();//module control action => map
        $this->mca2ncm();//namespace class method => map
    }


    public function url2raw()
    {
        $result = $this->matcher->result;
        if ($this->matcher_which === 'mca') {
            if (isset($result[0])) {
                $this->raw_module = $result[0];
            } else {
                $this->raw_module = '';
            }
            if (isset($result[1])) {
                $this->raw_control = $result[1];
            } else {
                $this->raw_control = '';
            }
            if (isset($result[2])) {
                $this->raw_action = $result[2];
            } else {
                $this->raw_action = '';
            }
        } else if ($this->matcher_which === 'ca') {
            $this->raw_module = '';
            if (isset($result[0])) {
                $this->raw_control = $result[0];
            } else {
                $this->raw_control = '';
            }
            if (isset($result[1])) {
                $this->raw_action = $result[1];
            } else {
                $this->raw_action = '';
            }
        } else if ($this->matcher_which === 'regexp') {
            $mask = $this->matcher->getMask();
            $mask = str_split($mask, 1);
            if (count($mask) == 2) {
                if (isset($result[$mask[0]])) {
                    $this->raw_control = $result[$mask[0]];
                } else {
                    $this->raw_control = '';
                }
                if (isset($result[$mask[1]])) {
                    $this->raw_action = $result[$mask[1]];
                } else {
                    $this->raw_action = '';
                }
            } else if (count($mask) == 3) {
                if (isset($result[$mask[0]])) {
                    $this->raw_module = $result[$mask[0]];
                } else {
                    $this->raw_module = '';
                }
                if (isset($result[$mask[1]])) {
                    $this->raw_control = $result[$mask[1]];
                } else {
                    $this->raw_control = '';
                }
                if (isset($result[$mask[2]])) {
                    $this->raw_action = $result[$mask[2]];
                } else {
                    $this->raw_action = '';
                }
            }
        }
    }


    public function raw2mca()
    {
        if ($this->raw_module === '') {
            $this->module = $this->m_default;
        } else {
            $this->module = $this->raw_module;
        }
        if ($this->raw_control === '') {
            $this->control = $this->c_default;
        } else {
            $this->control = $this->raw_control;
        }
        if ($this->raw_action === '') {
            $this->action = $this->a_default;
        } else {
            $this->action = $this->raw_action;
        }
    }

    public function mca2ncm()
    {
        //模块没有pattern
        $this->namespace = str_replace('{m}', $this->module, $this->namespace_pattern);
        $this->class = str_replace('{c}', $this->control, $this->class_pattern);
        $this->method = str_replace('{a}', $this->action, $this->method_pattern);
        //合法性处理
        $this->filter();
    }

    /**
     * //防止namespace class method前面有数字
     */
    public function filter()
    {
        $pattern = '/^[_a-zA-Z]\w+$/';
        $digital_pattern = '/^\d\w*$/';
        $filter_pattern = '/[^\w]/';
        $ns = explode('\\', $this->namespace);
        foreach ($ns as $k => $item) {
            if (!preg_match($pattern, $item)) {
                $tmp = preg_replace($filter_pattern, '', $item);
                if (preg_match($digital_pattern, $tmp)) {
                    $tmp = Ncm::DIGITAL_PREFIX . $tmp;
                }
                $ns[$k] = $tmp;
            }
        }

        if (!preg_match($pattern, $this->class)) {
            $this->class = preg_replace($filter_pattern, '', $this->class);
            if (preg_match($digital_pattern, $this->class)) {
                $this->class = Ncm::DIGITAL_PREFIX . $this->class;
            }
        }

        if (!preg_match($pattern, $this->method)) {
            $this->method = preg_replace($filter_pattern, '', $this->method);
            if (preg_match($digital_pattern, $this->method)) {
                $this->method = Ncm::DIGITAL_PREFIX . $this->method;
            }
        }
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}