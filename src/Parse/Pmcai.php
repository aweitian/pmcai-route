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

class Pmcai extends Parse
{
    protected $mask = "ca";
    protected $path;
    protected $module = '';
    protected $info = array();
    protected $control = "";
    protected $action = "";


    /**
     *
     * @param array $conf $http_entry-入口路径
     */
    public function __construct(array $conf = array())
    {
        if (isset ($conf ['http_entry'])) {
            $this->setPrefix($conf ['http_entry']);
        }
        if (isset ($conf ['mask'])) {
            $this->setMask($conf ['mask']);
        }
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->http_entry;
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
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * 设置PATHINFO，然后调用PARSE
     *
     * @param string $path
     * @param null $http_entry
     * @param null $mask
     * @return bool
     */
    protected function setPath($path, $http_entry = null, $mask = null)
    {
        $this->path = $path;
        $this->setPrefix($http_entry);
        $this->setMask($mask);
        return $this->isValidPath($this->path);
    }

    public function setMask($mask)
    {
        if (!is_null($mask)) {
            $this->mask = $mask;
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public function parse($path)
    {
        if (!$this->setPath($path))
            return false;
        if (!self::isValidMask($this->mask))
            return false;
        $this->module = '';
        $this->info = array();
        $mca_path = $this->realpath;
        $mca_arr = explode("/", trim($mca_path, "/"));
        $x = 0;
        $pmcaii_mask_arr = str_split($this->mask);
        while ($x < count($mca_arr)) {
            if ($x >= count($pmcaii_mask_arr))
                $z = "i";
            else
                $z = $pmcaii_mask_arr [$x];
            $v = $mca_arr [$x];
            switch ($z) {
                case "m" :
                    $this->module = $v;
                    break;
                case "c" :
                    $this->control = $v;
                    break;
                case "a" :
                    $this->action = $v;
                    break;
                case "i" :
                    $this->info [] = $v;
                    break;
            }
            $x++;
        }
        return true;
    }

    /**
     * @return \Aw\Routing\UrlGenerator\Pmcai
     */
    public function getUrl()
    {
         return new \Aw\Routing\UrlGenerator\Pmcai($this->http_entry,$this->module,$this->control,$this->action,$this->info);
    }

    public static function isValidMask($mask)
    {
        return preg_match("/^m?(ca|c)?$/", $mask);
    }
}
