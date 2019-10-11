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
    use Aw\Routing\Dispatch\AtCall;
    use Aw\Routing\Matcher\Callback;
    use Aw\Routing\Matcher\IMatcher;
    use Aw\Routing\Router;

    class RouteTest extends \PHPUnit_Framework_TestCase
    {
        public function testCa()
        {
            $router = new Router(new Request("/"));
            $route = $router->ca(array(), "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\main@index", $dispatcher->callback);
                $that->assertEquals("/", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("index", $res->getContent());


            $router = new Router(new Request("/foo"));
            $route = $router->ca(array(), "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/foo", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\foo@index", $dispatcher->callback);
                $that->assertEquals("/foo", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/foo', 'foo'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("ii", $res->getContent());


            $router = new Router(new Request("/foo/"));
            $route = $router->ca(array(), "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/foo/", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\foo@index", $dispatcher->callback);
                $that->assertEquals("/foo/", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/foo/', 'foo'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("ii", $res->getContent());


            $router = new Router(new Request("/foo/bar"));
            $route = $router->ca(array(), "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/foo/bar", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\foo@bar", $dispatcher->callback);
                $that->assertEquals("/foo/bar", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/foo/bar', 'foo', 'bar'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("ls", $res->getContent());
        }


        public function testMca()
        {
            $router = new Router(new Request("/"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals(404, $res->getStatusCode());


            $router = new Router(new Request("/control"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/control", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Control\\main@index", $dispatcher->callback);
                $that->assertEquals("/control", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/control', 'Control'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("index_", $res->getContent());


            $router = new Router(new Request("/control/"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/control/", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Control\\main@index", $dispatcher->callback);
                $that->assertEquals("/control/", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/control/', 'Control'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("index_", $res->getContent());


            $router = new Router(new Request("/control/foo"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/control/foo", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Control\\foo@index", $dispatcher->callback);
                $that->assertEquals("/control/foo", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/control/foo', 'Control', 'foo'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("ii_", $res->getContent());


            $router = new Router(new Request("/control/foo/bar"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/control/foo/bar", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Control\\foo@bar", $dispatcher->callback);
                $that->assertEquals("/control/foo/bar", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/control/foo/bar', 'Control', 'foo', 'bar'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("ls_", $res->getContent());


            $router = new Router(new Request("/mm"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/mm", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Mm\\main@index", $dispatcher->callback);
                $that->assertEquals("/mm", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/mm', 'Mm'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("_index_", $res->getContent());


            $router = new Router(new Request("/mm/"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/mm/", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Mm\\main@index", $dispatcher->callback);
                $that->assertEquals("/mm/", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/mm/', 'Mm'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("_index_", $res->getContent());


            $router = new Router(new Request("/mm/foo"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/mm/foo", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Mm\\foo@index", $dispatcher->callback);
                $that->assertEquals("/mm/foo", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/mm/foo', 'Mm', 'foo'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("_ii_", $res->getContent());


            $router = new Router(new Request("/mm/foo/bar"));
            $route = $router->mca(array(), "\\app\\(:1)\\(:2)@(:3)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/mm/foo/bar", $request->getPath());
                $that->assertTrue($matcher instanceof Callback);
                //还没有匹配
                $that->assertEquals(array(), $matcher->getMatchResult());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                $that->assertTrue($matcher instanceof Callback);
                $that->assertEquals("\\app\\Mm\\foo@bar", $dispatcher->callback);
                $that->assertEquals("/mm/foo/bar", $request->getPath());
                //匹配完成
                $that->assertEquals(array('/mm/foo/bar', 'Mm', 'foo', 'bar'), $matcher->getMatchResult());
            });
            $res = $router->run();
            $this->assertEquals("_ls_", $res->getContent());

        }


        public function testAtCall()
        {
            $router = new Router(new Request("/foo/bar"));
            $route = $router->get("/:var/:var", "\\app\\(:1)@(:2)");
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/foo/bar", $request->getPath());
                $that->assertFalse($matcher->hasUrlMatcher());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, AtCall $dispatcher) use ($that) {
                //匹配完成
                $that->assertTrue($matcher->hasUrlMatcher());
            });
            $res = $router->run();
            $this->assertEquals("ls", $res->getContent());
        }

        public function testGet()
        {
            $router = new Router(new Request("/"));
            $route = $router->get("/", function (Request $request, array $matches) {
                return 'get';
            });
            $that = $this;
            $route->getRouteHook()->addBeforeMatcherHook(function (Request $request, IMatcher $matcher) use ($that) {
                $that->assertEquals("/", $request->getPath());
                $that->assertFalse($matcher->hasUrlMatcher());
            });
            $route->getRouteHook()->addBeforeDispatcherHook(function (Request $request, IMatcher $matcher, \Aw\Routing\Dispatch\Callback $dispatcher) use ($that) {
                //匹配完成
                $that->assertTrue($matcher->hasUrlMatcher());
            });
            $res = $router->run();
            $this->assertEquals("get", $res->getContent());
        }

        public function testRegexp()
        {
            $router = new Router(new Request("/q/bar/12.html"));
            $that = $this;
            $router->match('#^/([a-zA-Z]\w*)/([a-zA-Z]\w*)/(\d+)\.html$#', function (Request $request, array $matches) use ($that) {
                $that->assertEquals(array("/q/bar/12.html", "q", "bar", "12"), $matches);
                return 'get';
            });

            $res = $router->run();
            $this->assertEquals("get", $res->getContent());

            $router = new Router(new Request("/p/q/r"));
            $router->match('#^/(p)/(q)/(r)$#', "\\p\\q@r");

            $res = $router->run();
            $this->assertEquals(array("/p/q/r", "p", "q", "r"), $res->getContent());
        }

        public function testHandle404()
        {
            $router = new Router(new Request("/q/bar/d12.html"));
            $that = $this;
            $router->match('#^/([a-zA-Z]\w*)/([a-zA-Z]\w*)/(\d+)\.html$#', function (Request $request, array $matches) use ($that) {
                return 'never will be executed';
            });
            $router->add404Handler(function (Request $request, Response $response) {
                $response->setStatusCode(200);
                $response->setContent("hook 404");
            });
            $res = $router->run();
            $this->assertEquals("hook 404", $res->getContent());
        }
    }
}

namespace p {
    class q
    {
        public function r(array $m)
        {
            return $m;
        }
    }
}

namespace app {

//    use Aw\Http\Request;

    class foo
    {
//        public function __construct(Request $request)
//        {
//        }
        public function index()
        {
            return 'ii';
        }

        public function bar()
        {
            return 'ls';
        }
    }


    class main
    {
        public function index()
        {
            return 'index';
        }
    }
}

namespace app\Control {
    class foo
    {
//        public function __construct(Request $request)
//        {
//        }
        public function index()
        {
            return 'ii_';
        }

        public function bar()
        {
            return 'ls_';
        }
    }


    class main
    {
        public function index()
        {
            return 'index_';
        }
    }
}

namespace app\Mm {
    class foo
    {
//        public function __construct(Request $request)
//        {
//        }
        public function index()
        {
            return '_ii_';
        }

        public function bar()
        {
            return '_ls_';
        }
    }


    class main
    {
        public function index()
        {
            return '_index_';
        }
    }
}
