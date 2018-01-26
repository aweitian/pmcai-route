<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月14日
 * @Desc: 把URL的PATH部分按 / 分割成数组
 */

namespace Aw\Routing\UrlGenerator;

class Pmi
{
    protected $moduleSkip = 0;
    protected $module = "";
    protected $info = "";
    protected $http_entry;

    public function __construct($http_entry,$module,$info,$moduleSkip)
    {
        $this->http_entry = $http_entry;
        $this->module = $module;
        $this->info = $info;
        $this->moduleSkip = $moduleSkip;
    }

    /**
     * @return int
     */
    public function getModuleSkip()
    {
        return $this->moduleSkip;
    }

    /**
     * @param int $moduleSkip
     * @return Pmi
     */
    public function setModuleSkip($moduleSkip)
    {
        $this->moduleSkip = $moduleSkip;
        return $this;
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
     * @return Pmi
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     * @return Pmi
     */
    public function setInfo($info)
    {
        $this->info = $info;
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
     * @return Pmi
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
        if (!$this->moduleSkip && $this->module) {
            $ret .= '/' . trim($this->module, '/');
        }
        if ($this->info) {
            $ret .= '/' . trim($this->info, '/');
        }
        return $ret;
    }
}