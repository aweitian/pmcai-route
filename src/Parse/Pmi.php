<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月26日
 * @Desc:
 *        把URL的M后面部分全部提取作为参数传递给模块的构造函数
 */

namespace Aw\Routing\Parse;

class Pmi extends Parse
{
    protected $moduleSkip = 0;

    protected $module = "";


    protected $info = "";
    protected $path;

    /**
     * @return $this
     */
    public function setModuleSkipOn()
    {
        $this->moduleSkip = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function setModuleSkipOff()
    {
        $this->moduleSkip = false;
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
     * @param $path
     * @return bool
     */
    public function parse($path)
    {
        if (!$this->isValidPath($path)) {
            return false;
        }
        $realpath = trim($this->realpath, "/");
        if (!$realpath) {
            $this->module = "";
            $this->info = '';
            return true;
        }
        if ($this->moduleSkip) {
            $this->module = "";
            $this->info = $realpath;
            return true;
        }
        $tmp = explode("/", trim($realpath, "/"), 2);
        if (count($tmp) == 2) {
            $this->module = $tmp[0];
            $this->info = $tmp[1];
        } else {
            $this->module = $tmp[0];
            $this->info = '';
        }
        return true;
    }


    /**
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