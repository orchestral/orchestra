<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class CoreTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$_SERVER['test.orchestra.started'] = null;
		$_SERVER['test.orchestra.done'] = null;

		// before we can manually test Orchestra\Core::start()
		// we need to shutdown Orchestra first.
		\Orchestra\Core::shutdown();
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
	 * Test Orchestra\Core::start() would fire events.
	 *
	 * @test
	 * @group core
	 */
	public function testStartMethod()
	{
		\Event::listen('orchestra.started', function ()
		{
			$_SERVER['test.orchestra.started'] = 'foo';
		});

		$this->assertNull($_SERVER['test.orchestra.started']);

		\Orchestra\Core::start();

		$memory = \Orchestra\Core::memory();
		$menu   = \Orchestra\Core::menu();

		$this->assertNotNull($memory);
		$this->assertInstanceOf('\Orchestra\Support\Memory\Driver', $memory);
		$this->assertNotNull($menu);
		$this->assertInstanceOf('\Orchestra\Widget\Menu', $menu);
		$this->assertEquals('foo', $_SERVER['test.orchestra.started']);

		\Orchestra\Core::shutdown();
	}

	/**
	 * Test Orchestra\Core::shutdown() triggers `orchestra.done`.
	 *
	 * @test
	 * @group core
	 */
	public function testShutdownMethod()
	{
		\Event::listen('orchestra.done', function ()
		{
			$_SERVER['test.orchestra.done'] = 'foo';
		});

		$this->assertNull($_SERVER['test.orchestra.done']);

		\Orchestra\Core::start();
		\Orchestra\Core::shutdown();

		$this->assertEquals('foo', $_SERVER['test.orchestra.done']);
	}

	/**
	 * Test validity of Orchestra\Core helper methods.
	 *
	 * @test
	 * @group core
	 */
	public function testHelperMethods()
	{
		\Orchestra\Core::start();

		$expected = \Orchestra\Widget::make('menu.orchestra');
		
		$this->assertEquals($expected, \Orchestra\Core::menu());
		$this->assertInstanceOf('\Orchestra\Widget\Driver', 
			\Orchestra\Core::menu());

		$expected = \Orchestra\Widget::make('menu.application');

		$this->assertEquals($expected, \Orchestra\Core::menu('app'));
		$this->assertInstanceOf('\Orchestra\Widget\Driver', 
			\Orchestra\Core::menu('app'));
		$this->assertInstanceOf('\Orchestra\Support\Memory\Driver', 
			\Orchestra\Core::memory());
		$this->assertInstanceOf('Orchestra\Support\Acl\Container', 
			\Orchestra\Core::acl());

		\Orchestra\Core::shutdown();
	}

	/**
	 * Test Configuration is properly configured.
	 *
	 * @test
	 * @group core
	 */
	public function testConfigurationIsProperlyConfigured()
	{
		\Orchestra\Core::start();
		
		$this->assertTrue(is_array(\Config::get('orchestra::support.form.fieldset')));

		\Orchestra\Core::shutdown();
	}
}
