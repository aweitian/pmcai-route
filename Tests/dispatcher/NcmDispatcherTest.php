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
    use Aw\Routing\Map\Ncm;

    class NcmDispatcherTest extends \PHPUnit_Framework_TestCase
    {
        public function testDispatcher()
        {
            $map = new Ncm("\\aw\\App", "main", "index");
            $dispatcher = new \Aw\Routing\Dispatch\Ncm($map);
            $this->assertTrue($dispatcher->dispatch(new Request("/qq")));
            $this->assertTrue($dispatcher->getResponse()->getContent() === '/qq');

            $map = new Ncm("\\aw\\App", "main", "non_exist");
            $dispatcher = new \Aw\Routing\Dispatch\Ncm($map);
            $this->assertFalse($dispatcher->dispatch(new Request("/qq")));
        }
    }
}


namespace aw\App {

    use Aw\Http\Request;

    class main
    {
        /**
         * @var Request
         */
        public $request;

        public function __construct($request)
        {
            $this->request = $request;
        }

        public function index()
        {
            return $this->request->getPath();
        }
    }
}
