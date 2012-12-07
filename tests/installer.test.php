<?php

class InstallerTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Orchestra\Installer::$status = false;
	}

	/**
	 * Test Orchestra\Installer::installed()
	 *
	 * @test
	 */
	public function testInstallationStatus()
	{
		Orchestra\Installer::$status = false;

		$this->assertFalse(Orchestra\Installer::installed());

		Orchestra\Installer::$status = true;

		$this->assertTrue(Orchestra\Installer::installed());
	}

	/**
	 * Test Orchestra\Installer::check_database()
	 *
	 * @test
	 */
	public function testCheckDatabase()
	{
		Config::set('database.default', 'sqlite');
		Config::set('database.connections.sqlite', array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		));
		
		$this->assertTrue(Orchestra\Installer::check_database());
	}
}