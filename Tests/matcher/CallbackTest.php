<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Aw\Http\Request;

class CallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Callback(function(Request $url){
            return $url->getPath() === "/ta";
        });
        $this->assertTrue($matcher->match(new Request("/ta")));
        //false
        $this->assertFalse($matcher->match(new Request("/ta/")));
        $this->assertFalse($matcher->match(new Request("/ta//")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa/bb")));
    }
}
