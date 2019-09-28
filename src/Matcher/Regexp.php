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
    const DELIMITER = '#';
    protected $regexp;
    public $result = array();

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
        if (!$this->regexp) {
            return false;
        }
        $url = $request->getPath();
        if (substr($this->regexp, 0, 1) !== self::DELIMITER || substr($this->regexp, -1, 1) !== self::DELIMITER) {
            $this->regexp = self::DELIMITER . $this->regexp . self::DELIMITER;
        }
        return !!preg_match($this->regexp, $url, $this->result);
    }
}