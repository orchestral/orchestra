<?php

class TestTheme extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		Bundle::start('orchestra');

		Orchestra\View::$theme = 'frontend';
	}

	/**
	 * Test Orchestra\Theme::__construct()
	 *
	 * @test
	 */
	public function testConstruct()
	{
		$theme = new Orchestra\Theme;

		$this->assertInstanceOf('Orchestra\Theme', $theme);
		$this->assertInstanceOf('Orchestra\Theme', IoC::resolve('orchestra.theme: frontend'));
		$this->assertInstanceOf('Orchestra\Theme', IoC::resolve('orchestra.theme: backend'));
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

	/**
	 * Teardown
	 */
	public function tearDown()
	{
		Orchestra\View::$theme = 'frontend';
	}
}