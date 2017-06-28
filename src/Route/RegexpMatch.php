<?php

/**
 * @Author: awei.tian
 * @Date: 2016年12月7日
 * 		
 * 依赖:
 */
namespace Tian\Route;

class RegexpMatch {
	use \Tian\LoggerTrait;
	public $matchedUrlPathInitArgs;
	public $url;
	private $rt;
	
	/**
	 * [
	 * 		"^\d+$" => [
	 * 			"http_entry" => "",
	 * 			"mask" => "ca",
	 * 		],
	 * 		...
	 * ]
	 * @param array $rt-["http_entry" => "","mask" => "ca"]
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
			if (preg_match ( "#^{$key}\$#", $this->url )) {
				! is_null ( $this->logger ) && $this->logger->debug ("route://regexp route matched.");
				$this->matchedUrlPathInitArgs = $item;
				! is_null ( $this->logger ) && $this->logger->debug ( "module://regexp route args is " . var_export ( $this->matchedUrlPathInitArgs, true ) );
				return true;
			}
		}
		return false;
	}
}