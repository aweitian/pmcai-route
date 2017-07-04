<?php
require __DIR__."/Route/EndWithMatch.php";
class RouterTest extends PHPUnit_Framework_TestCase {
	public function testPre() {
		//路由表由N项基本路由项组成，name => 基本路由项
		//基本路由项 由两个元素组成，第一个是类名或者定义在router中的别名
		//第二个是类的实例化参数
		$routetable = [
				'balabala' => [
						'startwith',
						[
								"/doctor" => 0,
								"/article" => [
										"http_entry" => "",
										"mask" => "mca"
								]
						]
				],
				'myendswith' => [
					'\Tian\Route\EndWithMatch',
					[
							"/gg/ff" => "u caught me"
					]
				],
				'rg' => [
					'regexp',
					[
							'^/(\w+)/(\d+)(/([\d\D]*))?$' => 'u'
					]
				],
				'default' => [
						'all',
						'i am a string argument'
				]
		];
		$router = new \Tian\Router();
		$router->setRouteTable($routetable);
		$routeName = $router->route("/doctor/aa/bb");
		$this->assertEquals($routeName, "balabala");
		$swRoute = $router->getRoute($routeName);
		$this->assertEquals(0, $swRoute->matchedUrlParseArgs);
		
		$routeName = $router->route("/article");
		$this->assertEquals($routeName, "balabala");
		$swRoute = $router->getRoute($routeName);
		$this->assertArraySubset([
				"http_entry" => "",
				"mask" => "mca"
		], $swRoute->matchedUrlParseArgs);
		
		
		$routeName = $router->route("/uk/aa/bb/gg/ff?ba=qq");
		$this->assertEquals($routeName, "myendswith");
		$swRoute = $router->getRoute($routeName);
		$this->assertEquals("u caught me", $swRoute->matchedUrlParseArgs);
		
		$routeName = $router->route("/uk/123/bb/gg/ff?ba=qq");
		$this->assertEquals($routeName, "myendswith");
		$swRoute = $router->getRoute($routeName);
		$this->assertEquals("u caught me", $swRoute->matchedUrlParseArgs);
		
		
		$routeName = $router->route("/uk/123/bb/gg/f1f?ba=qq");
		$this->assertEquals($routeName, "rg");
		$swRoute = $router->getRoute($routeName);
		$this->assertEquals("u", $swRoute->matchedUrlParseArgs);
		$this->assertArraySubset([
				"/uk/123/bb/gg/f1f",
				"uk",
				"123",
				"/bb/gg/f1f",
		], $swRoute->matches);
		
		$routeName = $router->route("/uk/1q23/bb/gg/f8f?ba=qq");
		$this->assertEquals($routeName, "default");
		$swRoute = $router->getRoute($routeName);
		$this->assertEquals("i am a string argument", $swRoute->matchedUrlParseArgs);
	}
}
