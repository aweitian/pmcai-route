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
    use Aw\Http\Response;
    use Aw\Routing\Dispatch\Callback;
    use Aw\Routing\Dispatch\IDispatcher;
    use Aw\Routing\Route;
    use Aw\Routing\Router\Router;

    class RouterCallbacksTest extends \PHPUnit_Framework_TestCase
    {
        public function testCallbackPoint()
        {
            $that = $this;
            $static = 0;
            $request = new Request('/p/a/bt');
            $router = new Router($request, array(), array(function (Request $request, $next) use ($that, &$static) {
                $that->assertEquals($static, 1);
                $static++;
                return $next($request);
            }));

            $router->get('/p', function () {
                return 'balabala';
            }, array(), Router::TYPE_MATCHER_STARTWITH);
            $router->post("/post", function () {
                return "POST";
            });


            $router->setCallbackRouterMatched(function (Route $route) use ($that) {
                $c = $route->getAction();
                $that->assertEquals($c(), 'balabala');
            });

            $router->setCallbackDispatcherCreated(function (IDispatcher $dispatcher) use ($that) {
                $that->assertTrue($dispatcher instanceof Callback);
            });

            $router->setCallbackBeforeThroughPreMiddleware(function () use ($that, &$static) {
                $that->assertEquals($static, 0);
                $static++;
            });

            $router->setCallbackAfterThroughPreMiddleware(function () use ($that, &$static) {
                $that->assertEquals($static, 2);
                $static++;
            });

            $router->setCallbackBeforeInvokeAction(function () use ($that, &$static) {
                $that->assertEquals($static, 3);
                $static++;
            });

            $router->setCallbackAfterInvokeAction(function (Response $response) use ($that, &$static) {
                $response->setContent('revised');
                $that->assertEquals($static, 4);
                $static++;
            });

            $router->setCallbackBeforeThroughPostMiddleware(function (Response $response) use ($that, &$static) {
                $response->setContent('revised');
                $that->assertEquals($static, 5);
                $static++;
            });

            $router->setCallbackAfterThroughPostMiddleware(function (Response $response) use ($that, &$static) {
                $that->assertEquals("revised", $response->getContent());
                $that->assertEquals($static, 6);
                $static++;
            });
            $this->assertEquals("revised", $router->run()->getContent());
            $this->assertEquals($request->carry['matcher'], 'a/bt');
        }

        public function testCallback500Point()
        {
            $that = $this;
            $request = new Request('/p/a/bt');
            $router = new Router($request, array(), array());

            $router->get('/p', function () {
                return 1 / 0;
            }, array(), Router::TYPE_MATCHER_STARTWITH);

            $router->setCallbackResponse500(function (Response $response, Exception $exception) use ($that) {
                //print $exception->getTraceAsString();
                $that->assertEquals("Division by zero", substr($response->getContent(), 0, 16));
                $that->assertEquals("Division by zero", substr($exception->getMessage(), 0, 16));
            });
            $router->run()->getContent();
        }

        public function testCallback404Point()
        {
            $that = $this;
            $request = new Request('/p/a/bt');
            $router = new Router($request, array(), array());

            $router->get('/po', function () {
                return 1 / 0;
            }, array(), Router::TYPE_MATCHER_STARTWITH);

            $router->setCallbackResponse404(function (Response $response) use ($that) {
                //print $exception->getTraceAsString();
                $that->assertEquals("Page not found", $response->getContent());
            });
            $router->run()->getContent();
        }
    }
}

