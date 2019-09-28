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
     * Callback constructor.
     * @param callable $callback
     */
    public function __construct(Callback $callback)
    {
        $this->callback = $callback;
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