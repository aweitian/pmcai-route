<?php

/**
 * @Author: awei.tian
 * @Date: 2016年12月7日
 * 		
 * 依赖:
 */
namespace Tian\Route;

class AllMatch {
	use \Tian\LoggerTrait;
	public $matchedUrlPathInitArgs;
	public $url;
	private $rt;
	
	/**
	 * [
	 * 		"http_entry" => ""
	 * 		"mask" => "ca"
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
		! is_null ( $this->logger ) && $this->logger->debug ( "module://default route args is " . var_export ( $this->rt, true ) );
		$this->url = $requestUri;
		$this->matchedUrlPathInitArgs = $this->rt;
		// ! is_null ( $this->logger ) && $this->logger->debug ( "module://pmcai C:" . $this->pmcai->control . ",A:" . $this->pmcai->action );
		return true;
	}
}