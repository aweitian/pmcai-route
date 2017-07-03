<?php

/**
 * @Author: awei.tian
 * @Date: 2016年12月7日
 * 		
 * 依赖:
 */
namespace Tian\Route;

class StartWithMatch {
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
	 *
	 * @return bool
	 */
	public function match($requestUri) {
		! is_null ( $this->logger ) && $this->logger->debug ( "module://URL is $requestUri" );
		$this->url = $requestUri;
		foreach ( $this->rt as $key => $item ) {
			if ((($temp = strlen ( $key ) - strlen ( "/" )) >= 0 && strpos ( $key, "/", $temp ) !== false)) {
				if (strpos ( $this->url, $key ) === 0) {
					! is_null ( $this->logger ) && $this->logger->debug ( "route://startwith route matched." );
					$this->matchedUrlParseArgs = $item;
					! is_null ( $this->logger ) && $this->logger->debug ( "module://startwith route args is " . var_export ( $this->matchedUrlParseArgs, true ) );
					return true;
				}
			} else {
				if ($key === $this->url || strpos ( $this->url, $key . "/" ) === 0) {
					! is_null ( $this->logger ) && $this->logger->debug ( "route://startwith route matched." );
					$this->matchedUrlParseArgs = $item;
					! is_null ( $this->logger ) && $this->logger->debug ( "module://startwith route args is " . var_export ( $this->matchedUrlParseArgs, true ) );
					return true;
				}
			}
		}
		return false;
	}
}