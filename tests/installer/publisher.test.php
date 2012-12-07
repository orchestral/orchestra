<?php

class InstallerPublisherTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Session::$instance = null;
		Session::load();

		Bundle::start('orchestra');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Session::$instance = null;
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