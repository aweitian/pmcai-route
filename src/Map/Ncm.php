<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/26
 * Time: 17:54
 * 类简单,开放所有方法
 */

namespace Aw\Routing\Map;

class Ncm implements INcm
{
    public $namespace;
    public $class;
    public $method;

    public function __construct($namespace = "\\App\\Control", $class = "main", $method = "index")
    {
        $this->class = $class;
        $this->method = $method;
        $this->namespace = $namespace;
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