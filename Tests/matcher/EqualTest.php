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

class EqualTest extends \PHPUnit_Framework_TestCase
{

    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Equal('/');
        $this->assertTrue($matcher->match(new Request("/")));
        $this->assertTrue($matcher->match(new Request("")));
        //false
        $this->assertFalse($matcher->match(new Request("a")));
        $this->assertFalse($matcher->match(new Request("/a")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa/bb")));


        $matcher = new \Aw\Routing\Matcher\Equal('/a');
        $this->assertTrue($matcher->match(new Request("/a")));
        $this->assertTrue($matcher->match(new Request("/a/")));
        //false
        $this->assertFalse($matcher->match(new Request("/")));
        $this->assertFalse($matcher->match(new Request("")));
        $this->assertFalse($matcher->match(new Request("a")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa/bb")));
        $this->assertFalse($matcher->match(new Request("/aa//")));
        $this->assertFalse($matcher->match(new Request("//aa/")));
        $this->assertFalse($matcher->match(new Request("//aa")));


        $matcher = new \Aw\Routing\Matcher\Equal('/aa/bb');
        $this->assertTrue($matcher->match(new Request("/aa/bb")));
        $this->assertTrue($matcher->match(new Request("/aa/bb/")));
        //false
        $this->assertFalse($matcher->match(new Request("/")));
        $this->assertFalse($matcher->match(new Request("")));
        $this->assertFalse($matcher->match(new Request("a")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa//bb")));
    }

}
