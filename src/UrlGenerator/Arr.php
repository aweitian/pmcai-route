<?php

/**
 * @Author: awei.tian
 * @Date: 2016年7月14日
 * @Desc: 把URL的PATH部分按 / 分割成数组
 */

namespace Aw\Routing\UrlGenerator;

class Arr
{
    protected $arr;
    protected $http_entry;

    public function __construct($http_entry,$arr)
    {
        $this->http_entry = $http_entry;
        $this->arr = $arr;
    }

    /**
     * 长度不在数组长度之内，在后面添加
     *
     * @param int $index
     * @param string $pathname
     * @return $this
     */
    public function set($index, $pathname)
    {
        if ($index < $this->getLength()) {
            $this->arr [$index] = $pathname;
        } else {
            $this->arr [] = $pathname;
        }
        return $this;
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

    protected function getHttpEntryUrl()
    {
        $url = trim($this->http_entry, '/');
        return $url;
    }
}