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
    use Aw\Routing\Matcher\Regexp;

    class CallbackDispatcherTest extends \PHPUnit_Framework_TestCase
    {
        public function testDispatcher()
        {
            $dispatcher = new Callback(function (Request $request, array $matches, $isDetect) {
                if ($isDetect) return true;
                return $request->getPath();
            });
            $this->assertTrue($dispatcher->detect(new Request("/qq")));
            $this->assertTrue($dispatcher->dispatch()->getContent() === '/qq');

            $dispatcher = new Callback(function (Request $request, array $matches, $isDetect) {
                if ($isDetect) return false;
                return $request->getPath() === "/non_exist";
            });
            $this->assertFalse($dispatcher->detect(new Request("/qq")));

            $dispatcher = new Callback(function (Request $request, array $matches, $isDetect) {
                if ($isDetect) return false;
                return $request->getPath();
            });
            $this->assertFalse($dispatcher->detect(new Request("/qq")));
        }
    }
}
