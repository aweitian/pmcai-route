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
    public static function CreateByMethodAndType($method, $type, $data)
    {
        $matcher = new AndCondition();
        if ($method !== "*") {
            $matcher->add(new Method($method));
            $type_matcher = self::CreateType($type, $data);
            if ($type_matcher != null) {
                $matcher->add($type_matcher);
            }
        }
        return $matcher;
    }

    private static function CreateType($type, $data)
    {
        switch ($type) {
            case Router::TYPE_MATCHER_EQUAL:
                return new Equal(array(
                    'url' => $data
                ));
            case Router::TYPE_MATCHER_REGEXP:
                return new Regexp(array(
                    'regexp' => Regexp::DELIMITOR . preg_quote($data, Regexp::DELIMITOR) . Regexp::DELIMITOR
                ));
            case Router::TYPE_MATCHER_STARTWITH:
                return new StartWith(array(
                    'prefix' => $data
                ));
        }
        return null;
    }
}