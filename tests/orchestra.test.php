<?php

class TestOrchestra extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		Config::set('database.default', 'sqlite');
		Config::set('database.connections.sqlite', array(
			'driver'   => 'sqlite',
			'database' => 'application',
			'prefix'   => '',
		));

		DB::$connections = array();

		Laravel\Session::load();
		
		Bundle::start('orchestra');

		Orchestra\Installer::$status = false;

		require_once "utils/setup.php";
	}

	/**
	 * Test Installation
	 *
	 * @test
	 */
	public function testInstallation()
	{
		$this->assertTrue(Orchestra\Installer::installed());

		$memory = Orchestra\Core::memory();
		$acl    = Orchestra\Core::acl();

		$this->assertInstanceOf('Hybrid\Memory\Fluent', $memory);
		$this->assertEquals('Orchestra', $memory->get('site.name'));
		$this->assertEquals('mail', $memory->get('email.default'));
		
		$this->assertInstanceOf('Hybrid\Acl', $acl);
	}

	public function tearDown()
	{
		File::delete(path('storage').'database'.DS.'application.sqlite');
	}
}