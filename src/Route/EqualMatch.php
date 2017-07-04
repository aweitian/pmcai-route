<?php

/**
 * @Author: awei.tian
 * @Date: 2016年12月7日
 * 		
 * 依赖:
 */
namespace Tian\Route;

class EqualMatch {
	use \Tian\LoggerTrait;
	public $matchedUrlParseArgs;
	public $url;
	private $rt;
	
	/**
	 * 这里的参数不一定是PMCAI的参数，也可以是别的参数
	 * 在这里，它就是数据，没有特殊的意义
	 * [
	 * "/path/to/dst" => [
	 * "http_entry" => "",
	 * "mask" => "ca",
	 * ],
	 * ...
	 * ]
	 * 
	 * @param array $rt        	
	 */
	public function __construct(array $rt) {
		$this->rt = $rt;
	}
	
	/**
	 * @param string $path-HTTP的PATH部分
	 * @return bool
	 */
	public function match($path) {
		! is_null ( $this->logger ) && $this->logger->debug ( "module://URL is $path" );
		$this->url = $path;
		foreach ( $this->rt as $key => $item ) {
			if ($key == $this->url) {
				! is_null ( $this->logger ) && $this->logger->debug ( "route://equal route matched." );
				$this->matchedUrlParseArgs = $item;
				! is_null ( $this->logger ) && $this->logger->debug ( "module://equal route args is " . var_export ( $this->matchedUrlParseArgs, true ) );
				return true;
			}
		}
		return false;
	}
}