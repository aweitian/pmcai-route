<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Aw\Http\Request;
    use Aw\Routing\Dispatch\Callback;
    use Aw\Routing\Map\Ncm;

    class CallbackDispatcherTest extends \PHPUnit_Framework_TestCase
    {
        public function testDispatcher()
        {
            $dispatcher = new Callback(function (Request $request){
                return $request->getPath();
            });
            $this->assertTrue($dispatcher->dispatch(new Request("/qq")));
            $this->assertTrue($dispatcher->getResponse()->getContent() === '/qq');

            $dispatcher = new Callback(function (Request $request){
                return $request->getPath() === "/non_exist";
            });
            $this->assertFalse($dispatcher->dispatch(new Request("/qq")));
        }
    }
}
