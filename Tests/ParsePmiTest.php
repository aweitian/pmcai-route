<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class ParsePmiTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPrefix()
    {
        $parse = new Aw\Routing\Parse\Pmi();
        $parse->setPrefix('/wx');
        $parse->setModuleSkipOff();
        $this->assertTrue($parse->parse('/wx/ctl/act/info1/info2'));

        $this->assertEquals('ctl',$parse->getModule());
        $this->assertEquals('act/info1/info2',$parse->getInfo());

        $parse->setModuleSkipOn();
        $this->assertTrue($parse->parse('/wx/ctl/act/info1/info2'));
        $this->assertEquals('',$parse->getModule());
        $this->assertEquals('ctl/act/info1/info2',$parse->getInfo());


        $parse->setModuleSkipOff();
        $this->assertTrue($parse->parse('/wx/ctl/act/info1/info2'));
        $this->assertEquals('/wx/ctl/info/ggb',$parse->getUrl()->setInfo('info/ggb')->getUrl());
    }

}
