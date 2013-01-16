<?php

Bundle::start('orchestra');

class ViewTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test instanceof Orchestra\View
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$view = new Orchestra\View;

		$this->assertInstanceOf('Laravel\View', $view);
		$this->assertEquals('frontend', Orchestra\View::$theme);
	}
}