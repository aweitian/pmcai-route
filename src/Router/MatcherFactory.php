<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/13
 * Time: 9:27
 */

namespace Aw\Routing\Router;


use Aw\Routing\Matcher\AndCondition;
use Aw\Routing\Matcher\Equal;
use Aw\Routing\Matcher\Method;
use Aw\Routing\Matcher\Regexp;
use Aw\Routing\Matcher\StartWith;

class MatcherFactory
{
    /**
     * @param $method
     * @param $type
     * @param $data
     * @return AndCondition
     */
    public static function CreateByMethodAndType($method,$type,$data)
    {
        $matcher = new AndCondition();
        $matcher->add(new Method($method));
        switch ($type) {
            case Router::TYPE_MATCHER_EQUAL:
                $matcher->add(new Equal(array(
                    'url' => $data
                )));
                break;
            case Router::TYPE_MATCHER_REGEXP:
                $matcher->add(new Regexp(array(
                    'regexp' => Regexp::DELIMITOR . preg_quote($data, Regexp::DELIMITOR) . Regexp::DELIMITOR
                )));
                break;
            case Router::TYPE_MATCHER_STARTWITH:
                $matcher->add(new StartWith(array(
                    'prefix' => $data
                )));
                break;
        }
        return $matcher;
    }
}