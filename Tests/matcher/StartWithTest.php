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

class StartWithTest extends \PHPUnit_Framework_TestCase
{

    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\StartWith('/');
        $this->assertTrue($matcher->match(new Request("/")));
        $this->assertTrue($matcher->match(new Request("")));
        $this->assertTrue($matcher->match(new Request("/a")));
        $this->assertTrue($matcher->match(new Request("/aa/")));
        $this->assertTrue($matcher->match(new Request("/aa/bb")));
        //false
        $this->assertFalse($matcher->match(new Request("a")));

        $matcher = new \Aw\Routing\Matcher\StartWith('/a');
        $this->assertTrue($matcher->match(new Request("/a")));
        $this->assertTrue($matcher->match(new Request("/a/")));
        $this->assertTrue($matcher->match(new Request("/a/bb")));
        $this->assertTrue($matcher->match(new Request("/a/bb/c/d")));
        //false
        $this->assertFalse($matcher->match(new Request("/")));
        $this->assertFalse($matcher->match(new Request("")));
        $this->assertFalse($matcher->match(new Request("a")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa/bb")));
        $this->assertFalse($matcher->match(new Request("/aa//")));
        $this->assertFalse($matcher->match(new Request("//aa/")));
        $this->assertFalse($matcher->match(new Request("//aa")));

    }

}
