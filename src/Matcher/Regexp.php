<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Regexp implements IRequestMatcher
{
    protected $regexp;
    protected $matches = array();
    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $url = $request->getPath();
        return !! preg_match($this->regexp,$url,$this->matches);
    }
}