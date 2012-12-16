<?php

Bundle::start('orchestra');

class AclTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Hybrid\Acl
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$acl = Orchestra\Acl::make();
		$this->assertInstanceOf('Hybrid\Acl\Container', $acl);
	}
}
