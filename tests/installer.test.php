<?php

class TestInstaller extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
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

	public function tearDown()
	{
		Orchestra\Installer::$status = false;
	}
}