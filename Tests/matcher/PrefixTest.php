<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class PrefixTest extends \PHPUnit_Framework_TestCase
{

    public function testPrefix()
    {
        $matcher = new \Aw\Routing\Matcher\Equal(array(
            'url' => "/ggfgg/abc"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/ggfgg/abc")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/ggfgg/abc/ccc")));
    }

}
