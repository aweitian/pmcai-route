<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class MatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testEqual()
    {
        $matcher = new \Aw\Routing\Matcher\Equal(array(
            'url' => "/test"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/test")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/test/")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/test/c")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/abc")));

    }

    public function testRegexp()
    {
        $matcher = new \Aw\Routing\Matcher\Regexp(array(
            'regexp' => "#^/$#"
        ));
        $ret = $matcher->match(new \Aw\Http\Request("/"));
        $this->assertTrue($ret);

        $matcher = new \Aw\Routing\Matcher\Regexp(array(
            'regexp' => "#^/prefix/\d+/aa$#"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/prefix/123/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
    }

    public function testPrefix()
    {
        $matcher = new \Aw\Routing\Matcher\Equal(array(
            'url' => "/ggfgg/abc"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abc/ccc")));
    }


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
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abccc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc/cc")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/abc/abc/ccc")));
    }


    public function testMapca()
    {
        $matcher = new \Aw\Routing\Matcher\Mapca(array(
            'prefix' => "/pre",
            'mask' => "ca",
            'type' => 'pmcai',
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre/abc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre/")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abc/ccc")));


        $matcher = new \Aw\Routing\Matcher\Mapca(array(
            'prefix' => "/pre",
            'mask' => "mca",
            'type' => 'pmcai',
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre/abc")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre/")));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/pre")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abc/ccc")));
    }
}
