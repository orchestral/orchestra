<?php

class TestTheme extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * test Orchestra\Theme::__construct()
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
}