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
	public $matchedUrlPathInitArgs;
	public $url;
	private $rt;
	
	/**
	 * 这里的参数不一定是PMCAI的参数，也可以是别的参数
	 * [
	 * 		"/path/to/dst" => [
	 * 			"http_entry" => "",
	 * 			"mask" => "ca",
	 * 		],
	 * 		...
	 * ]
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
			if ($key == $this->url) {
				! is_null ( $this->logger ) && $this->logger->debug ( "route://equal route matched." );
				$this->matchedUrlPathInitArgs = $item;
				! is_null ( $this->logger ) && $this->logger->debug ( "module://equal route args is " . var_export ( $this->matchedUrlPathInitArgs, true ) );
				return true;
			}
		}
		return false;
	}
}