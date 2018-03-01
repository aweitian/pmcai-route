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
    use Aw\Routing\Router\Router;

    class RouterTest extends \PHPUnit_Framework_TestCase
    {
        public function testRun()
        {
            $defined = array(
                function ($request, $next) {
                    $request->carry['df-mw-1'] = 'ok';
                    return $next($request);
                },
                function ($request, $next) {
                    $request->carry['df-mw-2'] = 'ok';
                    return $next($request);
                }
            );
            $g = array(
                function ($request, $next) {
                    $request->carry['g-mw-2'] = 'ok';
                    return $next($request);
                }
            );
            $request = new Request('/');
            $router = new Router($request, $defined, $g);
            $router->get('/', function () {
                return 'balabala';
            });

            $this->assertEquals("balabala", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
        }
    }
}

