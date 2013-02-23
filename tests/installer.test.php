<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class InstallerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Installer::$status = false;
	}

	/**
	 * Test Orchestra\Installer::installed()
	 *
	 * @test
	 * @group core
	 * @group installer
	 */
	public function testInstallationStatus()
	{
		\Orchestra\Installer::$status = false;

		$this->assertFalse(\Orchestra\Installer::installed());

		
		\Orchestra\Installer::$status = true;

		$this->assertTrue(\Orchestra\Installer::installed());
	}

	/**
	 * Test Orchestra\Installer::check_database()
	 *
	 * @test
	 * @group core
	 * @group installer
	 */
	public function testCheckDatabaseSuccessful()
	{
		\Config::set('database.default', 'sqlite');
		\Config::set('database.connections.sqlite', array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		));
		
		$this->assertTrue(\Orchestra\Installer::check_database());
	}

	/**
	 * Test Orchestra\Installer::check_database()
	 *
	 * @test
	 * @group core
	 * @group installer
	 */
	public function testCheckDatabaseFailed()
	{
		\Config::set('database.default', 'pgsql');
		\Config::set('database.connections.pgsql', array(
			'driver'   => 'pgsql',
			'database' => \Str::random(30),
			'username' => \Str::random(30),
			'password' => \Str::random(30),
			'prefix'   => '',
		));
		
		$this->assertFalse(\Orchestra\Installer::check_database());
	}
}