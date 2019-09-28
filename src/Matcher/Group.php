<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Group implements IMatcher
{
    protected $url;

    protected $array = array();

    public function add(IMatcher $matcher)
    {
        $this->array[] = $matcher;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        if (empty($this->array))
            return false;
        foreach ($this->array as $matcher) {
            if (!$matcher->match($request))
                return false;
        }
        return true;
    }
}