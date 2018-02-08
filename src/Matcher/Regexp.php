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
    public function __construct(array $data = array())
    {
        $attrs = 'regexp';
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
        $url = $request->getPath();
        return !! preg_match($this->regexp,$url,$this->matches);
    }

    public function getMatches()
    {
        return $this->matches;
    }
}