<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 12:43
 */

namespace Aw\Routing\Matcher;


use Aw\Http\Request;

class Ca implements IMatcher
{
    /**
     * @var array
     */
    public $result;


    /**
     * 路径必须匹配\w+
     * @param Request $request
     * @return bool
     */
    public function match(Request $request)
    {
        $url = $request->getPath();
        $url = trim($url);
        $url = trim($url, "/");
        if ($url === "") {
            $this->result = array();
        } else {
            $this->result = explode("/", trim($url, "/"));
            foreach ($this->result as $item) {
                if (!preg_match("/^\w+$/", $item)) {
                    return false;
                }
            }
        }
        return true;
    }
}