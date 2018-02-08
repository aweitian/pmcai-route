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
    protected $moduleSkip = false;
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
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
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
     * @return \Aw\Routing\UrlGenerator\Pmi
     */
    public function getUrl()
    {
        return new \Aw\Routing\UrlGenerator\Pmi($this->http_entry,$this->module,$this->info,$this->moduleSkip);
    }
}