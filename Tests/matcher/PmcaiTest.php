<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class PmcaiTest extends \PHPUnit_Framework_TestCase
{
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
