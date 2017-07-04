<?php

/**
 * @Author: awei.tian
 * @Date: 2016年12月7日
 * 		
 * 依赖:
 */
namespace Tian\Route;

class EndWithMatch {
	use \Tian\LoggerTrait;
	public $matchedUrlParseArgs;
	public $url;
	private $rt;
	
	/**
	 * 这里的参数不一定是PMCAI的参数，也可以是别的参数
	 * 在这里，它就是数据，没有特殊的意义
	 * [
	 * "/doctor" => [
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
			if ($this->endsWith($path, $key)) {
				$this->matchedUrlParseArgs = $item;
				return true;
			}
		}
		return false;
	}
	public function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen ( $haystack ) - strlen ( $needle )) >= 0 && strpos ( $haystack, $needle, $temp ) !== false);
	}
}