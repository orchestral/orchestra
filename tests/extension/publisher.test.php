<?php

class ExtensionPublisherTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test instance of Orchestra\Extension\Publisher default driver
	 *
	 * @test
	 */
	public function testInstanceOfDefaultDriver()
	{
		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'ftp');

		$this->assertInstanceOf('Orchestra\Extension\Publisher\Driver',	
			Orchestra\Extension\Publisher::driver());
		$this->assertInstanceOf('Orchestra\Extension\Publisher\FTP',	
			Orchestra\Extension\Publisher::driver());
	}
}