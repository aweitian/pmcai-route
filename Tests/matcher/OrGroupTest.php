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

class OrGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\OrGroup();
        $matcher->add(new \Aw\Routing\Matcher\Equal("/ta"));
        $matcher->add(new \Aw\Routing\Matcher\Method("POST"));

        $this->assertTrue($matcher->match(new Request("/ta")));
        $this->assertTrue($matcher->match(new Request("/ta","get")));
        $this->assertTrue($matcher->match(new Request("/ta","post")));
        $this->assertTrue($matcher->match(new Request("/gg/","POST")));
        $this->assertTrue($matcher->match(new Request("/gg","post")));
        $this->assertFalse($matcher->match(new Request("/gg")));

        $matcher = new \Aw\Routing\Matcher\OrGroup();
        $matcher->add(new \Aw\Routing\Matcher\Method("get"));
        $matcher->add(new \Aw\Routing\Matcher\Method("POST"));
        //false
        $this->assertTrue($matcher->match(new Request("/ta")));
        $this->assertTrue($matcher->match(new Request("/ta/")));
        $this->assertTrue($matcher->match(new Request("/ta/",'post')));
        $this->assertFalse($matcher->match(new Request("/aa",'put')));
        $this->assertFalse($matcher->match(new Request("/aa/bb","delete")));
    }
}
