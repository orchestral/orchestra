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
}