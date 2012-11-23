<?php

class ThemeTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');

		Orchestra\View::$theme = 'frontend';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Orchestra\View::$theme = 'frontend';
	}

	/**
	 * Test Orchestra\Theme::__construct()
	 *
	 * @test
	 */
	public function testConstruct()
	{
		$theme = new Orchestra\Theme\Container;

		$this->assertInstanceOf('Orchestra\Theme\Container', $theme);
		$this->assertInstanceOf('Orchestra\Theme\Container',
			IoC::resolve('orchestra.theme: frontend'));
		$this->assertInstanceOf('Orchestra\Theme\Container',
			IoC::resolve('orchestra.theme: backend'));
	}

	/**
	 * Test Orchestra\Theme::container()
	 *
	 * @test
	 */
	public function testContainer()
	{
		$this->assertEquals(Orchestra\Theme::resolve(),
			Orchestra\Theme::container(Orchestra\View::$theme));
	}

	/**
	 * Test Orchestra\Theme::resolve()
	 *
	 * @test
	 */
	public function testResolver()
	{
		$frontend = IoC::resolve('orchestra.theme: frontend');
		$backend  = IoC::resolve('orchestra.theme: backend');

		$this->assertFalse(Orchestra\Theme::resolve() === $backend);
		$this->assertTrue(Orchestra\Theme::resolve() === $frontend);

		Event::fire('orchestra.started: backend');

		$this->assertTrue(Orchestra\Theme::resolve() === $backend);
		$this->assertFalse(Orchestra\Theme::resolve() === $frontend);
	}
}
