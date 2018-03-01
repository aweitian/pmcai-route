<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class EqualTest extends \PHPUnit_Framework_TestCase
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
}
