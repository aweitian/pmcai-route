<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class StartWith implements IMatcher
{
    protected $prefix;
    public $path;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $prefix = rtrim($this->prefix, "/");
        $url = $request->getPath();
        if ($url == $prefix) {
            $this->path = '';
            return true;
        }
        $prefix = $prefix . '/';
        if (substr($url, 0, strlen($prefix)) === $prefix) {
            $this->path = substr($url, strlen($prefix));
            return true;
        }
        return false;
    }
}