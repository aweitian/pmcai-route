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
    use Aw\Routing\Matcher\IMatcher;
    use Aw\Routing\Router;

    class RouteTest extends \PHPUnit_Framework_TestCase
    {
        public function testCa()
        {
            $router = new Router(new Request("/foo/bar"));
            $route = $router->get("/:var/:var", "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request) use ($that) {
                $that->assertEquals("/foo/bar", $request->getPath());
            });
            $res = $router->run();
            $this->assertEquals("ls", $res->getContent());
        }

        public function testAtCall()
        {
//            $router = new Router(new Request("/foo/bar"));
//            //$map->method_pattern = '\\app';
//            $route = $router->get("/foo/bar","\\app\\foo@bar");
//            $that = $this;
//            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request) use ($that) {
//                $that->assertEquals("/foo/bar", $request->getPath());
//            });
//            $route->getRouteHook()->addBeforeMapHook(function (IMatcher $matcher, Request $request) use ($that) {
//                $that->assertTrue($matcher->match($request));
//            });
//            $route->getRouteHook()->addBeforeDispatcherHook(function (Cmr2Ncm $map, Request $request) use ($that) {
//                $that->assertEquals($map->getClass(), "foo");
//                $that->assertEquals($map->getMethod(), "bar");
//                $map->namespace = "\\app";
//                $that->assertEquals($map->getNamespace(), "\\app");
//            });
//            $res = $router->run();
//            $this->assertEquals("ls", $res->getContent());
        }
    }
}

namespace app {

    use Aw\Http\Request;

    class foo
    {
//        public function __construct(Request $request)
//        {
//        }

        public function bar()
        {
            return 'ls';
        }
    }
}

