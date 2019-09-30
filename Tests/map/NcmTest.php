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


        $matcher = new \Aw\Routing\Matcher\Mca();
        $class_pattern = '{c}Control';
        $method_pattern = '{a}Action';
        $namespace_pattern = '\\app\\{m}';

        $c_default = 'def';
        $a_default = "idx";
        $m_default = 'md';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/api/user")));
        $map->map();

        $this->assertEquals("api", $map->module);
        $this->assertEquals("api", $map->raw_module);

        $this->assertEquals("user", $map->control);
        $this->assertEquals("user", $map->raw_control);

        $this->assertEquals("idx", $map->action);
        $this->assertEquals("", $map->raw_action);

        $this->assertEquals("\\app\\api", $map->namespace);
        $this->assertEquals("userControl", $map->class);
        $this->assertEquals("idxAction", $map->method);


        $matcher = new \Aw\Routing\Matcher\Mca();
        $class_pattern = '{c}Control';
        $method_pattern = '{a}Action';
        $namespace_pattern = '\\app\\{m}';

        $c_default = 'def';
        $a_default = "idx";
        $m_default = 'md';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/api/user/add")));
        $map->map();

        $this->assertEquals("api", $map->module);
        $this->assertEquals("api", $map->raw_module);

        $this->assertEquals("user", $map->control);
        $this->assertEquals("user", $map->raw_control);

        $this->assertEquals("add", $map->action);
        $this->assertEquals("add", $map->raw_action);

        $this->assertEquals("\\app\\api", $map->namespace);
        $this->assertEquals("userControl", $map->class);
        $this->assertEquals("addAction", $map->method);

    }


    public function testCa()
    {
        $matcher = new \Aw\Routing\Matcher\Ca();
        $class_pattern = '{c}';
        $method_pattern = '{a}';
        $namespace_pattern = '\\app\\Control\\{m}';

        $c_default = 'main';
        $a_default = "index";
        $m_default = 'default';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/task/")));
        $map->map();

        $this->assertEquals("default", $map->module);
        $this->assertEquals("", $map->raw_module);

        $this->assertEquals("task", $map->control);
        $this->assertEquals("task", $map->raw_control);

        $this->assertEquals("index", $map->action);
        $this->assertEquals("", $map->raw_action);

        $this->assertEquals("\\app\\Control\\default", $map->namespace);
        $this->assertEquals("task", $map->class);
        $this->assertEquals("index", $map->method);


        $matcher = new \Aw\Routing\Matcher\Ca();
        $class_pattern = '{c}Control';
        $method_pattern = '{a}Action';
        $namespace_pattern = '\\app\\{m}';

        $c_default = 'def';
        $a_default = "idx";
        $m_default = 'md';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/api/user")));
        $map->map();

        $this->assertEquals("md", $map->module);
        $this->assertEquals("", $map->raw_module);

        $this->assertEquals("api", $map->control);
        $this->assertEquals("api", $map->raw_control);

        $this->assertEquals("user", $map->action);
        $this->assertEquals("user", $map->raw_action);

        $this->assertEquals("\\app\\md", $map->namespace);
        $this->assertEquals("apiControl", $map->class);
        $this->assertEquals("userAction", $map->method);


        $matcher = new \Aw\Routing\Matcher\Ca();
        $class_pattern = '{c}Control';
        $method_pattern = '{a}Action';
        $namespace_pattern = '\\app\\{m}';

        $c_default = 'def';
        $a_default = "idx";
        $m_default = 'md';
        $map = new \Aw\Routing\Map\Ncm($matcher, $class_pattern, $method_pattern, $namespace_pattern, $c_default, $a_default, $m_default);
        $this->assertTrue($matcher->match(new Request("/api/user/add")));
        $map->map();

        $this->assertEquals("md", $map->module);
        $this->assertEquals("", $map->raw_module);

        $this->assertEquals("api", $map->control);
        $this->assertEquals("api", $map->raw_control);

        $this->assertEquals("user", $map->action);
        $this->assertEquals("user", $map->raw_action);

        $this->assertEquals("\\app\\md", $map->namespace);
        $this->assertEquals("apiControl", $map->class);
        $this->assertEquals("userAction", $map->method);

    }
}
