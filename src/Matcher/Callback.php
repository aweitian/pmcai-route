<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Callback implements IMatcher
{
    protected $callback;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $attrs = 'callback';
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
        $callback = $this->callback;
        if (!is_callable($callback)) return false;
        return !!$callback($request);
    }
}