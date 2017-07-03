<?php
class RouterTest extends PHPUnit_Framework_TestCase {
	public function testPre() {
		$routetable = [
				'balabala' => [
						'startwith',
						[
								"/doctor" => []
						]
				],
				'default' => [
						'classname' => 'all'
				]
		];
		$log = new balabala();
		$this->assertEquals("debug: gg fu gg,lol\ndebug: g1g fu g1g,lal\n", $log->logger->getLog()) ;
	}
}
