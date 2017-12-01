<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tian\Route\Tests\Matcher;

use Tian\Container;
use Tian\Route\Exception\InvalidActionException;
use Tian\Route\Exception\MethodNotAllowedException;
use Tian\Route\Exception\ResourceNotFoundException;
use Tian\Route\Generator\UrlGenerator;
use Tian\Route\Matcher\UrlMatcher;
use Tian\Route\Route;
use Tian\Route\RouteCollection;
use Tian\Http\RequestContext;
use Tian\Http\Request;

class UrlGenTest extends \PHPUnit_Framework_TestCase
{
    public function testNoMethodSoAllowed()
    {
        $router = new RouteCollection();
        $router->get('/foo/{bar}', [function (){

        },'as' => 'get-foo']);

        try {
            $request = Request::create("/foo/bal");
            $router->dispatch($request);
            $requestContext = new RequestContext();
            $requestContext->fromRequest($request);
            $gen = new UrlGenerator($router,$requestContext);
            $url = $gen->generate("get-foo",[
                "bar" => "taw"
            ]);
            $this->assertEquals($url,"/foo/taw");
        } catch (MethodNotAllowedException $e) {
            echo 'MethodNotAllowedException';
        } catch (ResourceNotFoundException $e) {
            echo 'ResourceNotFoundException';
        }  catch (\Exception $e) {
            var_dump($e->getMessage());
        }

    }
}
