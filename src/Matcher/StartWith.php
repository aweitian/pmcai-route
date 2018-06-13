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
    protected $path;

    public function __construct(array $data = array())
    {
        $attrs = 'prefix';
        foreach (explode('|', $attrs) as $attr) {
            if (array_key_exists($attr, $data)) {
                $this->{$attr} = $data[$attr];
            }
        }
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
            $request->carry['matcher'] = $this->path;
            return true;
        }
        return false;
    }

    public function getPath()
    {
        return $this->path;
    }
}