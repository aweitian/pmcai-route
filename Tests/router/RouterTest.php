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
    use Aw\Routing\Router\Router;
    use g\gfgg\g;

    class RouterTest extends \PHPUnit_Framework_TestCase
    {
        public function testGet()
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
            $router->post("/post", function () {
                return "POST";
            });
            $this->assertEquals("balabala", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');


        }

        public function testActDef()
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
            $router->get('/', 'foo');
            $router->post("/post", function () {
                return "POST";
            });
            $this->assertEquals("index", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
        }

        public function testAct()
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
            $router->get('/', 'foo@uu');
            $router->post("/post", function () {
                return "POST";
            });
            $this->assertEquals("uug", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
        }

        public function testPost()
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
            $request = new Request('/post', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $this->assertEquals("POST", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');


        }

        public function testPmcai()
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
            $request = new Request('/foo/bar', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ));
            $this->assertEquals("f-br", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
        }

        public function testPmcaiGetInfo()
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
            $request = new Request('/foo/bar/inx/lol', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ));
            $this->assertEquals("f-br", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
            $this->assertEquals($request->carry['info'][0], 'inx');
            $this->assertEquals($request->carry['info'][1], 'lol');
        }


        public function testPmcaiNs()
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
            $request = new Request('/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfgg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ));
            $this->assertEquals("ggfgg", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
            $this->assertEquals(g::$c,1);
        }

        public function testMW()
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
                    $next($request);
                    $request->carry['g-mw-2'] = 'ok';
                    return 'kk';
                }
            );
            $request = new Request('/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/g/f', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfgg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ));
            $this->assertEquals("kk", $router->run()->getContent());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
            $this->assertEquals(g::$c,2);
        }

        public function testMWBlock()
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
                    //$next($request);
                    $request->carry['g-mw-2'] = 'ok';
                    return new Response("blocked",500);
                }
            );
            $request = new Request('/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/g/f', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfgg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ));
            $response = $router->run();
            $this->assertEquals("blocked", $response->getContent());
            $this->assertEquals(500, $response->getStatusCode());
            $this->assertEquals($request->carry['g-mw-2'], 'ok');
            //ACTION没有执行,所以这个值还是2
            $this->assertEquals(g::$c,2);
        }

        public function testPmcaiCheckDispatchFalse()
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
                    $ret = $next($request);
                    $request->carry['g-mw-2'] = 'ok';
                    return $ret;
                }
            );
            $request = new Request('/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/g/f', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\gfg\\lol\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ),array(
                //"check_dispatch" => true
            ));

            //这个路由不会执行到
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfgg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ),array(
                //"check_dispatch" => true
            ));

            $router->run();
            //ACTION没有执行,因为没有路由成功,所以这个值还是2
            $this->assertEquals(g::$c,2);
        }

        public function testPmcaiCheckDispatchTrue()
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
                    $ret = $next($request);
                    $request->carry['g-mw-2'] = 'ok';
                    return $ret;
                }
            );
            $request = new Request('/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/g/f', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\gfg\\lol\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ),array(
                "check_dispatch" => true
            ));
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfgg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ),array(
                "check_dispatch" => true
            ));

            $router->run();
            $this->assertEquals(g::$c,3);
        }
    }
}

namespace App\Controller {

    use Aw\Http\Request;

    class foo
    {
        public function bar(Request $request)
        {
            $request->carry['info'] = $request->carry['matcher']->getInfo();
            return 'f-br';
        }

        public function index()
        {
            return 'index';
        }

        public function uu()
        {
            return 'uug';
        }
    }
}
namespace g\gfgg
{
    class g
    {
        public static $c = 0;
        public function f()
        {
            self::$c++;
            return 'ggfgg';
        }
    }
}