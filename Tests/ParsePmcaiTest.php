<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class ParsePmcaiTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPrefix()
    {
        $parse = new Aw\Routing\Parse\Pmcai();
        $parse->setMask('mca');
        $this->assertTrue( $parse->parse('/module/ctl/act/info1/info2'));

        $this->assertEquals('',$parse->getPrefix());
        $this->assertEquals('module',$parse->getModule());
        $this->assertEquals('ctl',$parse->getControl());

        $this->assertEquals('act',$parse->getAction());
        $this->assertArraySubset(array('info1','info2'),$parse->getInfo());
    }


    public function testPrefix()
    {
        $parse = new Aw\Routing\Parse\Pmcai();
        $parse->setMask('ca');
        $parse->setPrefix('/p1/p2');
        $this->assertTrue( $parse->parse('/p1/p2/ctl/act/info1/info2'));

        $this->assertEquals('/p1/p2',$parse->getPrefix());
        $this->assertEquals('',$parse->getModule());
        $this->assertEquals('ctl',$parse->getControl());

        $this->assertEquals('act',$parse->getAction());
        $this->assertArraySubset(array('info1','info2'),$parse->getInfo());
    }


    public function testParseFail()
    {
        $parse = new Aw\Routing\Parse\Pmcai();
        $parse->setMask('ca');
        $parse->setPrefix('/p1/p2');
        $this->assertFalse($parse->parse('/p1/'));
        $this->assertFalse($parse->parse('/p1/p3'));
        $this->assertTrue($parse->parse('/p1/p2'));
        $parse->setMask('cca');
        $this->assertFalse($parse->parse('/p1/p2'));
        $parse->setMask('ca');
        $parse->setPrefix('');
        $this->assertTrue($parse->parse(''));
    }


    public function testDefault()
    {
        $parse = new Aw\Routing\Parse\Pmcai();
        $parse->setMask('ca');
        $parse->setPrefix('/p1/p2');
        $this->assertTrue($parse->parse('/p1/p2'));
        $this->assertEquals('',$parse->getControl());
        $this->assertEquals('',$parse->getAction());
        $this->assertEquals('',$parse->getModule());
        $this->assertTrue($parse->parse('/p1/p2/ctl'));
        $this->assertEquals('ctl',$parse->getControl());
        $this->assertEquals('',$parse->getAction());
        $this->assertEquals('',$parse->getModule());
    }

    public function testGetUrl()
    {
        $parse = new Aw\Routing\Parse\Pmcai();
        $parse->setMask('ca');
        $parse->setPrefix('/p1/p2');
        $this->assertTrue($parse->parse('/p1/p2'));
        $parse->setControl('control');
        $this->assertEquals('/p1/p2/control',$parse->getUrl());
    }
}
