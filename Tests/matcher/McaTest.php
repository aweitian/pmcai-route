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

class McaTest extends \PHPUnit_Framework_TestCase
{

    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Mca();


        $this->assertTrue($matcher->match(new Request("/a")));
        $this->assertTrue($matcher->match(new Request("/aa")));
        $this->assertTrue($matcher->match(new Request("/a/b")));
        $this->assertTrue($matcher->match(new Request("/aa/b")));
        $this->assertTrue($matcher->match(new Request("/aa/bbb")));
        $this->assertTrue($matcher->match(new Request("/aa/bbb/c")));
        $this->assertTrue($matcher->match(new Request("/aa/bbb/c/")));
        $this->assertTrue($matcher->match(new Request("/0aa/0bbb/c")));
        $this->assertTrue($matcher->match(new Request("/_0aa/0bbb/c")));
        $this->assertTrue($matcher->match(new Request("/gg-fgg")));
        $this->assertTrue($matcher->match(new Request("/gg-fgg/-hk")));
        $this->assertFalse($matcher->match(new Request("/gg-fg@g/-hk")));
        $this->assertFalse($matcher->match(new Request("/gg-fg=g/-hk")));
        $this->assertFalse($matcher->match(new Request("/gg-fg+g/-hk")));
        $this->assertFalse($matcher->match(new Request("/gg/&hk/k")));
        $this->assertFalse($matcher->match(new Request("/")));
    }

}
