<?php

Bundle::start('orchestra');

class InstallerPublisherTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Session::$instance = null;
		Session::load();
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
	public function testConstructPublisher()
	{
		$stub = new Orchestra\Installer\Publisher;

		$this->assertInstanceOf('Orchestra\Installer\Publisher', $stub);
	}
}