<?php

class AclTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test instanceof Hybrid\Acl
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$acl = Orchestra\Acl::make();
		$this->assertInstanceOf('Hybrid\Acl', $acl);
	}
}
