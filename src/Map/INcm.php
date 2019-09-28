<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Map;


interface INcm
{
    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return string
     */
    public function getMethod();
}