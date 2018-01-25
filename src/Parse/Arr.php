<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月14日
 * @Desc: 把URL的PATH部分按 / 分割成数组
 */

namespace Aw\Routing\Parse;

class Arr extends Parse
{
    public $raw;
    private $arr;

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
     * 返回长度
     *
     * @return number
     */
    public function getLength()
    {
        return count($this->arr);
    }

    /**
     * 长度不在数组长度之内，在后面添加
     *
     * @param int $index
     * @param string $pathname
     */
    public function set($index, $pathname)
    {
        if ($index < $this->getLength()) {
            $this->arr [$index] = $pathname;
        } else {
            $this->arr [] = $pathname;
        }
    }

    /**
     * 不存在返回NULL
     *
     * @param int $index
     * @return string NULL | pathname
     */
    public function get($index)
    {
        if ($index < $this->getLength()) {
            return $this->arr [$index];
        } else {
            return null;
        }
    }

    /**
     * 返回URL PATH部分
     *
     * @return string
     */
    public function getUrl()
    {
        $url = '/' . $this->getHttpEntryUrl() . '/' . join("/", $this->arr);
        return $url;
    }

}