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
	public $matchedUrlParseArgs;
	public $url;
	private $rt;
	
	/**
	 * 这里的参数不一定是PMCAI的参数，也可以是别的参数
	 * 在这里，它就是数据，没有特殊的意义
	 * [
	 * "http_entry" => ""
	 * "mask" => "ca"
	 * ]
	 *
	 * @param array $rt        	
	 */
	public function __construct($rt = ['http_entry' => '','mask'=>'ca']) {
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
		$this->matchedUrlParseArgs = $this->rt;
		// ! is_null ( $this->logger ) && $this->logger->debug ( "module://pmcai C:" . $this->pmcai->control . ",A:" . $this->pmcai->action );
		return true;
	}
}