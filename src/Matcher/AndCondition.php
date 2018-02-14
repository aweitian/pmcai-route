<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class AndCondition implements IMatcher
{
    protected $matchers = array();

    public function add(IMatcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        /**
         * @var IMatcher $matcher
         */
        foreach ($this->matchers as $matcher) {
            if (!$matcher->match($request)) {
                return false;
            }
        }
        return true;
    }
}