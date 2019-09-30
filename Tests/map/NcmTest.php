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

class NcmTest extends \PHPUnit_Framework_TestCase
{
    public function testMca()
    {
        $matcher = new \Aw\Routing\Matcher\Mca();
        $class_pattern = '{c}Control';
        $method_pattern = '{a}Action';
        $namespace_pattern = '\\app\\{m}';

        $c_default = 'def';
        $a_default = "idx";
        $m_default = 'md';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/api/")));
        $map->map();

        $this->assertEquals("api", $map->module);
        $this->assertEquals("api", $map->raw_module);

        $this->assertEquals("def", $map->control);
        $this->assertEquals("", $map->raw_control);

        $this->assertEquals("idx", $map->action);
        $this->assertEquals("", $map->raw_action);

        $this->assertEquals("\\app\\api", $map->namespace);
        $this->assertEquals("defControl", $map->class);
        $this->assertEquals("idxAction", $map->method);


    }
}
