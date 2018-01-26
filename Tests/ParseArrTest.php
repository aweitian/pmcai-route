<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class ParseArrTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPrefix()
    {
        $parse = new Aw\Routing\Parse\Arr();
        $parse->setPrefix('/wx');

        $this->assertTrue($parse->parse('/wx/ctl/act/info1/info2'));

        $this->assertEquals('ctl',$parse->get(0));
        $this->assertEquals('info1',$parse->get(2));
        $url = $parse->getUrl()->set(2,'info11');
        $this->assertEquals('/wx/ctl/act/info11/info2',$url->getUrl());
    }

}
