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

class RegexpTest extends \PHPUnit_Framework_TestCase
{

    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Regexp('#^/(\w+)(/(\w+)\.html)?$#');
        $this->assertTrue($matcher->match(new Request("/abc")));
        $this->assertEquals(array("/abc", "abc"), $matcher->result);
        $this->assertTrue($matcher->match(new Request("/0ab/0.html")));
        $this->assertEquals(array("/0ab/0.html", "0ab", "/0.html", "0"), $matcher->result);
        $this->assertTrue($matcher->match(new Request("/10ab/01a.html")));
        $this->assertEquals(array("/10ab/01a.html", "10ab", "/01a.html", "01a"), $matcher->result);
        //false
        $this->assertFalse($matcher->match(new Request("/10/ab/01a.html")));
        $this->assertEquals(array(), $matcher->result);//匹配不成功会返回为空
        $this->assertFalse($matcher->match(new Request("/ab/01a")));
        $this->assertFalse($matcher->match(new Request("/abc/")));

        //
        $matcher = new \Aw\Routing\Matcher\Regexp('#^/api((/\w+)*)$#');
        $this->assertTrue($matcher->match(new Request("/api")));
        $this->assertEquals(array("/api", ""), $matcher->result);
        $this->assertTrue($matcher->match(new Request("/api/cc/uu")));
        $this->assertEquals(array("/api/cc/uu", "/cc/uu", "/uu"), $matcher->result);
        $this->assertTrue($matcher->match(new Request("/api/10ab/01a/a/b/c")));
        $this->assertEquals(array("/api/10ab/01a/a/b/c", "/10ab/01a/a/b/c", "/c"), $matcher->result);

        $this->assertFalse($matcher->match(new Request("/10/ab/01a")));
        $this->assertEquals(array(), $matcher->result);//匹配不成功会返回为空

        $this->assertFalse($matcher->match(new Request("/ab/01a")));
    }

}
