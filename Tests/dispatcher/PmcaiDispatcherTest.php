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
    use Aw\Routing\Dispatch\PmcaiDispatcher;
    use Aw\Routing\Matcher\Mapca;

    class PmcaiDispatcherTest extends \PHPUnit_Framework_TestCase
    {
        public function testDefault()
        {
            $dispatcher = new PmcaiDispatcher(array(
                'namespace_map' => array(
                    PmcaiDispatcher::DEFAULT_MODULE => '\\App\\Controller\\'
                )
            ));
            $request = new Request('/');
            $matcher = new Mapca();
            $matcher->match($request);
            $this->assertEquals("main-index",$dispatcher->dispatch($request)->getContent());
        }

        public function testCtl()
        {
            $dispatcher = new PmcaiDispatcher(array(
                'namespace_map' => array(
                    PmcaiDispatcher::DEFAULT_MODULE => '\\App\\Controller\\'
                )
            ));
            $request = new Request('/ctl');
            $matcher = new Mapca();
            $matcher->match($request);
            $this->assertEquals("ctl-index",$dispatcher->dispatch($request)->getContent());
        }

        public function testAct()
        {
            $dispatcher = new PmcaiDispatcher(array(
                'namespace_map' => array(
                    PmcaiDispatcher::DEFAULT_MODULE => '\\App\\Controller\\'
                )
            ));
            $request = new Request('/ctl/act');
            $matcher = new Mapca();
            $matcher->match($request);
            $this->assertEquals("ctl-act-index",$dispatcher->dispatch($request)->getContent());
        }
    }
}

namespace App\Controller
{
    class mainControl
    {
        public function indexAction()
        {
            return 'main-index';
        }
    }

    class ctlControl
    {
        public function indexAction()
        {
            return 'ctl-index';
        }
        public function actAction()
        {
            return 'ctl-act-index';
        }
    }
}
