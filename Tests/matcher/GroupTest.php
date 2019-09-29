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

class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $matcher = new \Aw\Routing\Matcher\Group();
        $matcher->add(new \Aw\Routing\Matcher\Equal("/ta"));
        $matcher->add(new \Aw\Routing\Matcher\Method("POST"));

        $this->assertTrue($matcher->match(new Request("/ta","post")));
        $this->assertTrue($matcher->match(new Request("/ta/","POST")));
        //false
        $this->assertFalse($matcher->match(new Request("/ta")));
        $this->assertFalse($matcher->match(new Request("/ta/")));
        $this->assertFalse($matcher->match(new Request("/ta//")));
        $this->assertFalse($matcher->match(new Request("/aa/")));
        $this->assertFalse($matcher->match(new Request("/aa/bb")));
    }
}
