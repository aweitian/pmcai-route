<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class StartWithTest extends \PHPUnit_Framework_TestCase
{

    public function testStartwith()
    {
        $matcher = new \Aw\Routing\Matcher\StartWith(array(
            'prefix' => "/ggfgg/abc"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/abc")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abccc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/cc")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/abc/abc/ccc")));
        $matcher = new \Aw\Routing\Matcher\StartWith(array(
            'prefix' => "/ggfgg/abc/"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/abc")));
        $this->assertEquals("abc", $matcher->getPath());
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abccc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/cc")));
        $this->assertEquals("cc", $matcher->getPath());
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/abc/abc/ccc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/cc/ff")));
        $this->assertEquals("cc/ff", $matcher->getPath());
    }

}
