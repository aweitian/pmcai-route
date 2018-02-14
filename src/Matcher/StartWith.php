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
        if ($url == $prefix) return true;
        $prefix = $prefix . '/';
        return substr($url, 0, strlen($prefix)) === $prefix;
    }
}