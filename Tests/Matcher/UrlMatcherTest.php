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
use Tian\Route\Exception\MethodNotAllowedException;
use Tian\Route\Exception\ResourceNotFoundException;
use Tian\Route\Matcher\UrlMatcher;
use Tian\Route\Route;
use Tian\Route\RouteCollection;
use Tian\Http\RequestContext;
use Tian\Http\Request;

class UrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    private function createContext($pathinfo="")
    {
        $r = new RequestContext();
        if ($pathinfo)
            $r->setPathInfo($pathinfo);
        return $r;
    }
    public function testNoMethodSoAllowed()
    {
        $router = new RouteCollection();
        $router->get('foo', ["uses" => "\\Tian\\Main@Index","as" => "get-foo"]);

        $matcher = new UrlMatcher($router,$this->createContext()->setPathInfo("/foo"));
        $data =  $matcher->match();

        $this->assertEquals(array('_route' => 'get-foo'), $data);
    }

    public function testMethodNotAllowed()
    {
        $coll = new RouteCollection();
        $coll->get('foo', ["uses" => "\\Tian\\Main@Index","as" => "get-foo"]);

        $matcher = new UrlMatcher($coll);

        try {
            $matcher->match($this->createContext()->setPathInfo("/foo")->setMethod('post'));
            $this->fail();
        } catch (MethodNotAllowedException $e) {
            $this->assertEquals(array('GET'), $e->getAllowedMethods());
        }
    }

    public function testHeadAllowedWhenRequirementContainsGet()
    {
        $coll = new RouteCollection();
        $coll->add('foo', new Route('/foo', array(), array(), array(), '', array(), array('get')));

        $matcher = new UrlMatcher($coll);
        $matcher->setContext($this->createContext()->setPathInfo("/foo"));
        $this->assertInternalType('array', $matcher->match());
    }

    public function testMethodNotAllowedAggregatesAllowedMethods()
    {
        $coll = new RouteCollection();
        $coll->add('foo1', new Route('/foo', array(), array(), array(), '', array(), array('post')));
        $coll->add('foo2', new Route('/foo', array(), array(), array(), '', array(), array('put', 'delete')));

        $matcher = new UrlMatcher($coll);
        $matcher->setContext($this->createContext()->setPathInfo("/foo"));
        try {
            $matcher->match();
            $this->fail();
        } catch (MethodNotAllowedException $e) {
            $this->assertEquals(array('POST', 'PUT', 'DELETE'), $e->getAllowedMethods());
        }
    }

    public function testMatchResult()
    {
        $router = new RouteCollection();
        $router->get('/{foo}/{bar}',function (){
            return "abc";
        });
        //$collection->add('bar', new Route('/{foo}/{bar}', array('foo' => 'foo', 'bar' => 'bar'), array()));
        $response = $router->dispatch(Request::create("/a/b"));

        $this->assertEquals("abc",$response->getContent());
    }

    public function testMatchResultWithParameters()
    {
        $router = new RouteCollection();
        $router->get('/{foo}/{bar}',function (Container $app,$bar, $foo, classParameter $cls){
            return  $app->make("\\Tian\\Route\\Tests\\Matcher\\classParameter")->getVar().$bar."-abc-".$foo."-".$cls->getVar();
        });
        //$collection->add('bar', new Route('/{foo}/{bar}', array('foo' => 'foo', 'bar' => 'bar'), array()));
        $response = $router->dispatch(Request::create("/a/b"));

        $this->assertEquals("myvarb-abc-a-myvar",$response->getContent());
    }

    public function testMatchResultWithParametersStringCall()
    {
        $router = new RouteCollection();
        $router->get('/{bar}/{lol}',"\\Tian\\Route\\Tests\\Matcher\\classParameter@action");
        $response = $router->dispatch(Request::create("/a/bl"));

        $this->assertEquals("a-bl",$response->getContent());
    }

    public function testMiddlewareResult()
    {
        $router = new RouteCollection();
        $router->get('/{bar}/{lol}',["\\Tian\\Route\\Tests\\Matcher\\classParameter@action",
            "middleware" => [
                function ($request,$next) {
                    var_dump($request);
                    return $next();
                }
            ]
            ]);
        $matcher = new UrlMatcher($router);
        $matcher->setContext($this->createContext("/foo/bar"));
        $result = $matcher->match();
        $this->assertEquals(array(
            "bar"=>"foo",
            "lol"=> "bar",
            "_route"=> "get /{bar}/{lol}"
        ),$result);
        //获取传递进去的ACTION
        $matchedRoute = $router->getRoute($result['_route']);
        $this->assertEquals(array(0,"middleware"),array_keys($matchedRoute->getOption('_call')));
    }

    public function testMiddleware()
    {
        $router = new RouteCollection();
        $router->get('/{bar}/{lol}',["\\Tian\\Route\\Tests\\Matcher\\classParameter@middleware",
            "middleware" => [
                function (Request $request,$next) {
                    $request->attributes->add([
                        'aa' => 'bb'
                    ]);
                    return $next($request);
                }
            ]
        ]);
        $response = $router->dispatch(Request::create("/abar/blol"));
        $this->assertEquals("abar-blol",$response->getContent());
        $this->assertEquals("bb",$router->getRequest()->attributes->get('aa'));
    }
    public function testGroup()
    {
        $router = new RouteCollection();
        $router->group(['a' => 'b'],function (RouteCollection $router){
            $router->get('/{bar}/{lol}',["\\Tian\\Route\\Tests\\Matcher\\classParameter@middleware",
                "middleware" => [
                    function (Request $request,$next) {
                        $request->attributes->add([
                            'aa' => 'bb'
                        ]);
                        return $next($request);
                    }
                ]
            ]);
            $response = $router->dispatch(Request::create("/abar/blol"));
            $this->assertEquals("abar-blol",$response->getContent());
            $this->assertEquals("bb",$router->getRequest()->attributes->get('aa'));
            $call = $router->getContainer()->make('route.matched.action');
            $this->assertEquals('b',($call['a']));
        });

    }


//    public function testMatch()
//    {
//        // test the patterns are matched and parameters are returned
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/foo/{bar}'));
//        $matcher = new UrlMatcher($collection, $this->createContext()->setPathInfo("/no-match"));
//        try {
//            $matcher->match();
//            $this->fail();
//        } catch (ResourceNotFoundException $e) {
//        }
//        $this->assertEquals(array('_route' => 'foo', 'bar' => 'baz'), $matcher->match('/foo/baz'));
//
//        // test that defaults are merged
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/foo/{bar}', array('def' => 'test')));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'foo', 'bar' => 'baz', 'def' => 'test'), $matcher->match('/foo/baz'));
//
//        // test that route "method" is ignored if no method is given in the context
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/foo', array(), array(), array(), '', array(), array('get', 'head')));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertInternalType('array', $matcher->match('/foo'));
//
//        // route does not match with POST method context
//        $matcher = new UrlMatcher($collection, new RequestContext('', 'post'));
//        try {
//            $matcher->match('/foo');
//            $this->fail();
//        } catch (MethodNotAllowedException $e) {
//        }
//
//        // route does match with GET or HEAD method context
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertInternalType('array', $matcher->match('/foo'));
//        $matcher = new UrlMatcher($collection, new RequestContext('', 'head'));
//        $this->assertInternalType('array', $matcher->match('/foo'));
//
//        // route with an optional variable as the first segment
//        $collection = new RouteCollection();
//        $collection->add('bar', new Route('/{bar}/foo', array('bar' => 'bar'), array('bar' => 'foo|bar')));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'bar', 'bar' => 'bar'), $matcher->match('/bar/foo'));
//        $this->assertEquals(array('_route' => 'bar', 'bar' => 'foo'), $matcher->match('/foo/foo'));
//
//        $collection = new RouteCollection();
//        $collection->add('bar', new Route('/{bar}', array('bar' => 'bar'), array('bar' => 'foo|bar')));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'bar', 'bar' => 'foo'), $matcher->match('/foo'));
//        $this->assertEquals(array('_route' => 'bar', 'bar' => 'bar'), $matcher->match('/'));
//
//        // route with only optional variables
//        $collection = new RouteCollection();
//        $collection->add('bar', new Route('/{foo}/{bar}', array('foo' => 'foo', 'bar' => 'bar'), array()));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'bar', 'foo' => 'foo', 'bar' => 'bar'), $matcher->match('/'));
//        $this->assertEquals(array('_route' => 'bar', 'foo' => 'a', 'bar' => 'bar'), $matcher->match('/a'));
//        $this->assertEquals(array('_route' => 'bar', 'foo' => 'a', 'bar' => 'b'), $matcher->match('/a/b'));
//    }
//
//    public function testMatchWithPrefixes()
//    {
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/{foo}'));
//        $collection->addPrefix('/b');
//        $collection->addPrefix('/a');
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'foo', 'foo' => 'foo'), $matcher->match('/a/b/foo'));
//    }

//    public function testMatchWithDynamicPrefix()
//    {
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/{foo}'));
//        $collection->addPrefix('/b');
//        $collection->addPrefix('/{_locale}');
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_locale' => 'fr', '_route' => 'foo', 'foo' => 'foo'), $matcher->match('/fr/b/foo'));
//    }
//
//    public function testMatchSpecialRouteName()
//    {
//        $collection = new RouteCollection();
//        $collection->add('$péß^a|', new Route('/bar'));
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => '$péß^a|'), $matcher->match('/bar'));
//    }
//
//    public function testMatchNonAlpha()
//    {
//        $collection = new RouteCollection();
//        $chars = '!"$%éà &\'()*+,./:;<=>@ABCDEFGHIJKLMNOPQRSTUVWXYZ\\[]^_`abcdefghijklmnopqrstuvwxyz{|}~-';
//        $collection->add('foo', new Route('/{foo}/bar', array(), array('foo' => '['.preg_quote($chars).']+')));
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'foo', 'foo' => $chars), $matcher->match('/'.rawurlencode($chars).'/bar'));
//        $this->assertEquals(array('_route' => 'foo', 'foo' => $chars), $matcher->match('/'.strtr($chars, array('%' => '%25')).'/bar'));
//    }
//
//    public function testMatchWithDotMetacharacterInRequirements()
//    {
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/{foo}/bar', array(), array('foo' => '.+')));
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        $this->assertEquals(array('_route' => 'foo', 'foo' => "\n"), $matcher->match('/'.urlencode("\n").'/bar'), 'linefeed character is matched');
//    }
//
//    public function testMatchOverriddenRoute()
//    {
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/foo'));
//
//        $collection1 = new RouteCollection();
//        $collection1->add('foo', new Route('/foo1'));
//
//        $collection->addCollection($collection1);
//
//        $matcher = new UrlMatcher($collection, new RequestContext());
//
//        $this->assertEquals(array('_route' => 'foo'), $matcher->match('/foo1'));
//        $this->setExpectedException('Tian\Route\Exception\ResourceNotFoundException');
//        $this->assertEquals(array(), $matcher->match('/foo'));
//    }
//
//    public function testMatchRegression()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo/{foo}'));
//        $coll->add('bar', new Route('/foo/bar/{foo}'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $this->assertEquals(array('foo' => 'bar', '_route' => 'bar'), $matcher->match('/foo/bar/bar'));
//
//        $collection = new RouteCollection();
//        $collection->add('foo', new Route('/{bar}'));
//        $matcher = new UrlMatcher($collection, new RequestContext());
//        try {
//            $matcher->match('/');
//            $this->fail();
//        } catch (ResourceNotFoundException $e) {
//        }
//    }
//
//    public function testDefaultRequirementForOptionalVariables()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{page}.{_format}', array('page' => 'index', '_format' => 'html')));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $this->assertEquals(array('page' => 'my-page', '_format' => 'xml', '_route' => 'test'), $matcher->match('/my-page.xml'));
//    }
//
//    public function testMatchingIsEager()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{foo}-{bar}-', array(), array('foo' => '.+', 'bar' => '.+')));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $this->assertEquals(array('foo' => 'text1-text2-text3', 'bar' => 'text4', '_route' => 'test'), $matcher->match('/text1-text2-text3-text4-'));
//    }
//
//    public function testAdjacentVariables()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{w}{x}{y}{z}.{_format}', array('z' => 'default-z', '_format' => 'html'), array('y' => 'y|Y')));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        // 'w' eagerly matches as much as possible and the other variables match the remaining chars.
//        // This also shows that the variables w-z must all exclude the separating char (the dot '.' in this case) by default requirement.
//        // Otherwise they would also consume '.xml' and _format would never match as it's an optional variable.
//        $this->assertEquals(array('w' => 'wwwww', 'x' => 'x', 'y' => 'Y', 'z' => 'Z', '_format' => 'xml', '_route' => 'test'), $matcher->match('/wwwwwxYZ.xml'));
//        // As 'y' has custom requirement and can only be of value 'y|Y', it will leave  'ZZZ' to variable z.
//        // So with carefully chosen requirements adjacent variables, can be useful.
//        $this->assertEquals(array('w' => 'wwwww', 'x' => 'x', 'y' => 'y', 'z' => 'ZZZ', '_format' => 'html', '_route' => 'test'), $matcher->match('/wwwwwxyZZZ'));
//        // z and _format are optional.
//        $this->assertEquals(array('w' => 'wwwww', 'x' => 'x', 'y' => 'y', 'z' => 'default-z', '_format' => 'html', '_route' => 'test'), $matcher->match('/wwwwwxy'));
//
//        $this->setExpectedException('Tian\Route\Exception\ResourceNotFoundException');
//        $matcher->match('/wxy.html');
//    }
//
//    public function testOptionalVariableWithNoRealSeparator()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/get{what}', array('what' => 'All')));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//
//        $this->assertEquals(array('what' => 'All', '_route' => 'test'), $matcher->match('/get'));
//        $this->assertEquals(array('what' => 'Sites', '_route' => 'test'), $matcher->match('/getSites'));
//
//        // Usually the character in front of an optional parameter can be left out, e.g. with pattern '/get/{what}' just '/get' would match.
//        // But here the 't' in 'get' is not a separating character, so it makes no sense to match without it.
//        $this->setExpectedException('Tian\Route\Exception\ResourceNotFoundException');
//        $matcher->match('/ge');
//    }
//
//    public function testRequiredVariableWithNoRealSeparator()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/get{what}Suffix'));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//
//        $this->assertEquals(array('what' => 'Sites', '_route' => 'test'), $matcher->match('/getSitesSuffix'));
//    }
//
//    public function testDefaultRequirementOfVariable()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{page}.{_format}'));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//
//        $this->assertEquals(array('page' => 'index', '_format' => 'mobile.html', '_route' => 'test'), $matcher->match('/index.mobile.html'));
//    }
//
//    /**
//     * @expectedException \Tian\Route\Exception\ResourceNotFoundException
//     */
//    public function testDefaultRequirementOfVariableDisallowsSlash()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{page}.{_format}'));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//
//        $matcher->match('/index.sl/ash');
//    }
//
//    /**
//     * @expectedException \Tian\Route\Exception\ResourceNotFoundException
//     */
//    public function testDefaultRequirementOfVariableDisallowsNextSeparator()
//    {
//        $coll = new RouteCollection();
//        $coll->add('test', new Route('/{page}.{_format}', array(), array('_format' => 'html|xml')));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//
//        $matcher->match('/do.t.html');
//    }
//
//    /**
//     * @expectedException \Tian\Route\Exception\ResourceNotFoundException
//     */
//    public function testSchemeRequirement()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo', array(), array(), array(), '', array('https')));
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $matcher->match('/foo');
//    }
//
//    public function testDecodeOnce()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo/{foo}'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $this->assertEquals(array('foo' => 'bar%23', '_route' => 'foo'), $matcher->match('/foo/bar%2523'));
//    }
//
//    public function testCannotRelyOnPrefix()
//    {
//        $coll = new RouteCollection();
//
//        $subColl = new RouteCollection();
//        $subColl->add('bar', new Route('/bar'));
//        $subColl->addPrefix('/prefix');
//        // overwrite the pattern, so the prefix is not valid anymore for this route in the collection
//        $subColl->get('bar')->setPath('/new');
//
//        $coll->addCollection($subColl);
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $this->assertEquals(array('_route' => 'bar'), $matcher->match('/new'));
//    }
//
//    public function testWithHost()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo/{foo}', array(), array(), array(), '{locale}.example.com'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
//        $this->assertEquals(array('foo' => 'bar', '_route' => 'foo', 'locale' => 'en'), $matcher->match('/foo/bar'));
//    }
//
//    public function testWithHostOnRouteCollection()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo/{foo}'));
//        $coll->add('bar', new Route('/bar/{foo}', array(), array(), array(), '{locale}.example.net'));
//        $coll->setHost('{locale}.example.com');
//
//        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
//        $this->assertEquals(array('foo' => 'bar', '_route' => 'foo', 'locale' => 'en'), $matcher->match('/foo/bar'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
//        $this->assertEquals(array('foo' => 'bar', '_route' => 'bar', 'locale' => 'en'), $matcher->match('/bar/bar'));
//    }
//
//    /**
//     * @expectedException \Tian\Route\Exception\ResourceNotFoundException
//     */
//    public function testWithOutHostHostDoesNotMatch()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/foo/{foo}', array(), array(), array(), '{locale}.example.com'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'example.com'));
//        $matcher->match('/foo/bar');
//    }
//
//    /**
//     * @expectedException \Tian\Route\Exception\ResourceNotFoundException
//     */
//    public function testPathIsCaseSensitive()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/locale', array(), array('locale' => 'EN|FR|DE')));
//
//        $matcher = new UrlMatcher($coll, new RequestContext());
//        $matcher->match('/en');
//    }
//
//    public function testHostIsCaseInsensitive()
//    {
//        $coll = new RouteCollection();
//        $coll->add('foo', new Route('/', array(), array('locale' => 'EN|FR|DE'), array(), '{locale}.example.com'));
//
//        $matcher = new UrlMatcher($coll, new RequestContext('', 'GET', 'en.example.com'));
//        $this->assertEquals(array('_route' => 'foo', 'locale' => 'en'), $matcher->match('/'));
//    }
}

class classParameter
{
    public function getVar()
    {
        return "myvar";
    }
    public function action(Container $app,$bar,$lol)
    {
        return $bar.'-'.$lol;

    }
    public function middleware($bar,$lol)
    {
        return $bar.'-'.$lol;

    }
}
