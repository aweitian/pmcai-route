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
    use Aw\Routing\Router\Router;
    use g\gfg\g;

    class ModuleMatchTest extends \PHPUnit_Framework_TestCase
    {
        public function testPmcaiCheckDispatchFalse()
        {
            $defined = array();
            $g = array();

            //这个地方模块名为y,不能匹配
            $request = new Request('/y/g/f', 'POST');
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
            ),array(
                "check_dispatch" => true,
                'mask' => 'mca',
                'module' => 'x'
            ));
            $router->run();
            $this->assertEquals(g::$c,0);
        }
        public function testPmcaiCheckDispatchTrue()
        {
            $defined = array();
            $g = array();
            $request = new Request('/x/g/f', 'POST');
            $router = new Router($request, $defined, $g);
            $router->get('/g/f', function () {
                return 'balabala';
            });
            $router->post("/post", function () {
                return "POST";
            });
            $router->pmcai('/', array(), array(
                'namespace' => '\\g\\gfg\\',
                'ctl_tpl' => '{}',
                'act_tpl' => '{}'
            ),array(
                "check_dispatch" => true,
                'mask' => 'mca',
                'module' => 'x'
            ));
            $router->run();
            $this->assertEquals(g::$c,1);
        }


    }
}


namespace g\gfg
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