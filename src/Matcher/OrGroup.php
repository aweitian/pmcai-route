<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class OrGroup implements IMatcher
{
    protected $url;

    protected $array = array();

    protected $url_matcher = null;

    public $hasUrlMatcher = false;

    public function add(IMatcher $matcher)
    {
        $this->array[] = $matcher;
    }

    public function addUrlMatcher(IMatcher $matcher)
    {
        $this->hasUrlMatcher = true;
        $this->url_matcher = $matcher;
        $this->add($matcher);
    }

    public function getUrlMatcher()
    {
        return $this->url_matcher;
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
            if ($matcher->match($request))
                return true;
        }
        return false;
    }
}