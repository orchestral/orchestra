<?php

class TestInstaller extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		\Laravel\Session::load();

		Config::set('database.default', 'memory');
		Config::set('database.connections.memory', array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		));

		Laravel\Database::$connections = array();
		Laravel\Database::connection(null);
		
		Bundle::start('orchestra');
	}

	/**
	 * Test Installation
	 *
	 * @test
	 */
	public function testInstallation()
	{
		/* require_once "utils/setup.php"; */
	}

	/**
	 * Test Orchestra\Installer::installed()
	 *
	 * @test
	 */
	public function testStatus()
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
		$this->assertTrue(Orchestra\Installer::check_database());
	}
}