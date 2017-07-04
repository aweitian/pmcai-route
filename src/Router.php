<?php

/**
 * 
 * @date 2017/7/1 08:54:58
 * 处理路由表
 * 
 */
namespace Tian;

class Router {
	use \Tian\LoggerTrait;
	protected $routeTable = [ ];
	protected $routes = [ ];
	private $routeInsts = [ ];
	public $alias = [ 
			"all" => "\Tian\Route\AllMatch",
			"equal" => "\Tian\Route\EqualMatch",
			"regexp" => "\Tian\Route\RegexpMatch",
			"startwith" => "\Tian\Route\StartWithMatch" 
	];
	/**
	 *
	 * @param string $name        	
	 * @param route $route        	
	 * @return \Tian\Router
	 */
	public function addRoute($name, $route) {
		$this->routes [$name] = $route;
		return $this;
	}
	/**
	 *
	 * @return \Tian\Router
	 */
	public function clearRouteTable() {
		$this->routeTable = [ ];
		return $this;
	}
	
	/**
	 * 设置路由表,两个元素，一个是路由类名，一个是路由类初始化的参数
	 * 虽然第二个参数大多数是URLPARSE参数，但不局限于此，
	 * 这里就是传递一个数据进去进行匹配，匹配成功，返回匹配的数据
	 * [
	 * "default" => ["all",["http_entry" => "","mask" => "ca"]]
	 *
	 * ]
	 *
	 * @param array $rt        	
	 * @return \Tian\Router
	 */
	public function setRouteTable(array $rt) {
		$this->routeTable = $rt;
		return $this;
	}
	
	/**
	 * 正常情况这个参数
	 *
	 * @param array $arg        	
	 * @return \Tian\Router
	 */
	public function addDefaultRoute($arg = null) {
		if (! is_array ( $arg ))
			$arg = [ 
					"all",
					[ 
							"http_entry" => "",
							"mask" => "ca" 
					] 
			];
		
		return $this->addRoute ( "default", $arg );
	}
	/**
	 * 返回路由实例
	 * @param string $name
	 * @return route
	 */
	public function getRoute($name) {
		return $this->routeInsts[$name];
	}
	/**
	 * 参数是httprequest 的 requestUri
	 * @param string $url        	
	 * @return string
	 */
	public function route($url) {
		$arr = explode("?", $url);
		$url = $arr[0];
		foreach ( $this->routeTable as $rn => $rt ) {
			if (array_key_exists ( $rn, $this->routeInsts )) {
				$route = $this->routeInsts [$rn];
			} else {
				if (count ( $rt ) !== 2) {
					! is_null ( $this->logger ) && $this->logger->error ( "router://invalid route table item,require `classname`,key:$rn" );
					continue;
				}
				$cls = array_key_exists ( $rt [0], $this->alias ) ? $this->alias [$rt [0]] : $rt [0];
				$route = new $cls ( $rt [1] );
				$this->routeInsts [$rn] = $route;
			}
			if ($route->match ( $url ))
				return $rn;
		}
		return null;
	}
}