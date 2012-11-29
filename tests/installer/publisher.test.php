<?php

class InstallerPublisherTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test Orchestra\Installer\Publisher can be constructed.
	 *
	 * @test
	 */
	public function testConstructInstance()
	{
		$this->assertInstanceOf('Orchestra\Installer\Publisher', 
			new Orchestra\Installer\Publisher);
	}
}