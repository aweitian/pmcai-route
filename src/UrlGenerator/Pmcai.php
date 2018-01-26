<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月14日
 * @Desc: 把URL的PATH部分按 / 分割成数组
 */

namespace Aw\Routing\UrlGenerator;

class Pmcai
{
    protected $module = '';
    protected $info = array();
    protected $control = "";
    protected $action = "";
    protected $http_entry;

    public function __construct($http_entry,$module,$control,$action,$info)
    {
        $this->http_entry = $http_entry;
        $this->module = $module;
        $this->control = $control;
        $this->action = $action;
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     * @return Pmcai
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param array $info
     * @return Pmcai
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return string
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param string $control
     * @return Pmcai
     */
    public function setControl($control)
    {
        $this->control = $control;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return Pmcai
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHttpEntry()
    {
        return $this->http_entry;
    }

    /**
     * @param mixed $http_entry
     * @return Pmcai
     */
    public function setHttpEntry($http_entry)
    {
        $this->http_entry = $http_entry;
        return $this;
    }

    /**
     * 返回URL PATH部分
     *
     * @return string
     */
    public function getUrl()
    {
        $ret = '';
        if ($this->http_entry) {
            $ret .= '/' . trim($this->http_entry, '/');
        }
        if (!empty ($this->module)) {
            $ret .= '/' . $this->module;
        }
        if ($this->control) {
            $ret .= '/' . $this->control;
        }
        if ($this->action) {
            $ret .= '/' . $this->action;
        }
        if (!empty ($this->info)) {
            $ret .= '/' . implode('/', $this->info);
        }
        return $ret;
    }

}