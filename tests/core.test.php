<?php

class CoreTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
		$_SERVER['test.orchestra.started'] = null;
		$_SERVER['test.orchestra.done'] = null;

		// before we can manually test Orchestra\Core::start()
		// we need to shutdown Orchestra first.
		Orchestra\Core::shutdown();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['test.orchestra.started']);
		unset($_SERVER['test.orchestra.done']);
	}

	/**
	 * Test Orchestra\Core::start() is properly done.
	 *
	 * @test
	 */
	public function testStartingUpOrchestra()
	{
		Event::listen('orchestra.started', function ()
		{
			$_SERVER['test.orchestra.started'] = 'foo';
		});

		Orchestra\Core::start();

		$memory = Orchestra\Core::memory();
		$menu   = Orchestra\Core::menu();

		$this->assertNotNull($memory);
		$this->assertInstanceOf('Hybrid\Memory\Driver', $memory);

		$this->assertNotNull($menu);
		$this->assertInstanceOf('Orchestra\Widget\Menu', $menu);
	
		$this->assertEquals('foo', $_SERVER['test.orchestra.started']);
	}

	/**
	 * Test Orchestra\Core::shutdown() triggers `orchestra.done`.
	 *
	 * @test
	 */
	public function testShutdownTriggerEvent()
	{
		Event::listen('orchestra.done', function ()
		{
			$_SERVER['test.orchestra.done'] = 'foo';
		});

		Orchestra\Core::shutdown();

		$this->assertEquals('foo', $_SERVER['test.orchestra.done']);
	}
}
