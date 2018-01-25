<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class PrefixEqual implements IRequestMatcher
{
    protected $prefix;

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
        $url = $request->getPath();
        return substr($url, 0, strlen($url)) === $this->prefix;
    }
}