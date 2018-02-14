<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 11:26
 * 这个类计算MW
 */

namespace Aw\Routing\Router;
class Middleware
{
    protected $pipelines = array();

    protected $global = array();
    protected $defined = array();
    protected $private = array();

    protected $use_global = true;

    /**
     * Middleware constructor.
     * @param array $data global|defined|private|use_global=true
     */
    public function __construct(array $data = array())
    {
        $attr = 'global|defined|private|use_global';
        foreach (explode('|', $attr) as $attr) {
            if (isset($data[$attr]) && is_array($data[$attr])) {
                $this->{$attr} = $data[$attr];
            }
        }
    }

    public function getMiddleware($private = null, $global = null)
    {
        if (!is_null($private)) {
            $this->setPrivate($private);
        }
        if (is_bool($global)) {
            $this->setUseGlobal($global);
        }
        $ret = array();
        if ($this->use_global === true) {
            $ret = $this->global;
        }
        $mw = $this->private;
        if (is_string($mw))
            $mw = array($mw);
        foreach ($mw as $item) {
            $cell = $this->getMw($item);
            if (is_array($cell)) {
                $ret = array_merge($ret, $cell);
            } else {
                $ret[] = $cell;
            }
        }
        return $ret;
    }

    protected function getMw($mw)
    {
        if (is_string($mw) && isset($this->defined[$mw])) {
            return $this->defined[$mw];
        } else {
            return $mw;
        }
    }

    /**
     * @return array
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @param array $global
     * @return Middleware
     */
    public function setGlobal($global)
    {
        $this->global = $global;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefined()
    {
        return $this->defined;
    }

    /**
     * @param array $defined
     * @return Middleware
     */
    public function setDefined($defined)
    {
        $this->defined = $defined;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param array $private
     * @return Middleware
     */
    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseGlobal()
    {
        return $this->use_global;
    }

    /**
     * @param bool $use_global
     * @return Middleware
     */
    public function setUseGlobal($use_global)
    {
        $this->use_global = $use_global;
        return $this;
    }
}