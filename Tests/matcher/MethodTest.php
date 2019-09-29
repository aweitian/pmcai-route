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

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Method();

        $this->assertTrue($matcher->match(new Request("/ta")));
        $this->assertTrue($matcher->match(new Request("/ta/","get")));
        $this->assertTrue($matcher->match(new Request("/","get")));
        //false
        $this->assertFalse($matcher->match(new Request("/ta",'POST')));
        $this->assertFalse($matcher->match(new Request("/",'DELETE')));


        $matcher = new \Aw\Routing\Matcher\Method('post');

        $this->assertTrue($matcher->match(new Request("/ta",'POST')));
        $this->assertTrue($matcher->match(new Request("/ta",'post')));
        $this->assertTrue($matcher->match(new Request("/ta/","POST")));
        $this->assertTrue($matcher->match(new Request("/","post")));
        //false
        $this->assertFalse($matcher->match(new Request("/ta",'get')));
        $this->assertFalse($matcher->match(new Request("/")));
    }
}
