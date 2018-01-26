<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月14日
 * @Desc: 把URL的PATH部分按 / 分割成数组
 */

namespace Aw\Routing\Parse;

class Arr extends Parse
{
    protected $arr;

    /**
     * @param $path
     * @return bool
     */
    public function parse($path)
    {
        if ($this->isValidPath($path)) {
            $this->arr = explode("/", trim($this->realpath, "/"));
            return true;
        }
        return false;
    }

    /**
     * 不存在返回NULL
     *
     * @param int $index
     * @return string NULL | pathname
     */
    public function get($index)
    {
        if ($index < count($this->arr)) {
            return $this->arr [$index];
        } else {
            return null;
        }
    }

    /**
     * @return \Aw\Routing\UrlGenerator\Arr
     */
    public function getUrl()
    {
        return new \Aw\Routing\UrlGenerator\Arr($this->http_entry,$this->arr);
    }

}