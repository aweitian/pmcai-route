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
    use Aw\Routing\Dispatch\AtCall;
    use Aw\Routing\Dispatch\Callback;
    use Aw\Routing\Map\Ncm;

    class AtCallDispatcherTest extends \PHPUnit_Framework_TestCase
    {
        public function testDispatcher()
        {
            $dispatcher = new AtCall("main@index","\\ax\\App");
            $this->assertTrue($dispatcher->dispatch(new Request("/qq")));
            $this->assertTrue($dispatcher->getResponse()->getContent() === '/qq');

            $dispatcher = new AtCall("class@method");
            $this->assertFalse($dispatcher->dispatch(new Request("/qq")));

            $dispatcher = new AtCall("\\ax\\App\\main@index");
            $this->assertTrue($dispatcher->dispatch(new Request("/qq")));

            $dispatcher = new AtCall("\\ax\\App\\main");
            $this->assertTrue($dispatcher->dispatch(new Request("/qq")));
        }
    }
}


namespace ax\App {

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
