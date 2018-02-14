<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPrefix()
    {
        $request = new \Aw\Http\Request('/');
        $router = new \Aw\Routing\Router($request);
        $router->get("/",function (){
            echo "hello world";
        });
        $router->run();
        var_dump($router->match_logs);
        var_dump($request->carry);
    }

}
