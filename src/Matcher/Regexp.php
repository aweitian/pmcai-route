<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Regexp implements IMatcher
{
    const DELIMITOR = '#';
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
        if (!$this->regexp) {
            return false;
        }
        if (substr($this->regexp, 0, 1) === self::DELIMITOR && substr($this->regexp, -1, 1) === self::DELIMITOR) {
            $url = $request->getPath();
            if (preg_match($this->regexp, $url, $this->matches)) {
                $request->carry['matcher'] = $this->matches;
                return true;
            }
        }
        return false;
    }

    public function getMatches()
    {
        return $this->matches;
    }
}