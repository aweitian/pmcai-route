<?php

/**
 * @author awei.tian
 * date: 2013-9-16
 * 说明:
 *    本类主要是按PMCAI规则解析 URL的PATH部分
 *    P为HTTP入口目录
 *    m为模块存放目录
 *    c为控制器名称
 *    a为动作名称
 *    i为动作后剩余部分,称为INFO
 */

namespace Aw\Routing\Parse;

class Parse
{
    protected $http_entry = "";
    protected $http_entry_len = 0;
    /**
     * 去掉HTTP_ENTRY的URL PATH部分
     *
     * @var string
     */
    protected $realpath;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->http_entry;
    }

    public function setPrefix($http_entry)
    {
        if (!is_null($http_entry)) {
            $this->http_entry = $http_entry;
            $this->http_entry_len = strlen($this->http_entry);
        }
    }

    public function isValidPath($path)
    {
        if ($this->http_entry_len) {
            if (substr($path, 0, $this->http_entry_len) !== $this->http_entry) {
                return false;
            } else {
                $this->realpath = substr($path, $this->http_entry_len);
            }
        } else {
            $this->realpath = $path;
        }
        return true;
    }

    public function getRealPath()
    {
        return $this->realpath;
    }

    protected function getHttpEntryUrl()
    {
        $url = trim($this->http_entry, '/');
        return $url;
    }
}
