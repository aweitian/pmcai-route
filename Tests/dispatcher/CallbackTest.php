<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace
{

    use Aw\Http\Request;
    use Aw\Routing\Dispatch\Callback;

    class CallbackTest extends \PHPUnit_Framework_TestCase
    {
        public function testRun()
        {
            $dispatcher = new Callback(function (){
                return "foo-bar";
            });
            $this->assertEquals("foo-bar",$dispatcher->dispatch(new Request())->getContent());
        }
    }
}


