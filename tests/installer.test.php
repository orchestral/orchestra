<?php

class TestInstaller extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setup()
	{
		Bundle::start('orchestra');

		Orchestra\Installer::$status = false;

		Config::set('database', array(
			'default' => 'memory',
			'connections' => array(
				'memory' => array(
					'driver'   => 'sqlite',
					'database' => ':memory:',
					'prefix'   => '',
				),
			),
		));

	}

	/**
	 * Test Orchestra\Installer::installed()
	 *
	 * @test
	 */
	public function testStatus()
	{
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

	/**
	 * Test Installation
	 *
	 * @test
	 */
	public function testInstallation()
	{
		require_once "utils/setup.php";
	}
}