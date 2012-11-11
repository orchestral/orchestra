<?php

class WidgetTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}
	
	/**
	 * Test Orchestra\Widget::make()
	 * 
	 * @test
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Orchestra\Widget\Menu', Orchestra\Widget::make('menu'));
		$this->assertInstanceOf('Orchestra\Widget\Pane', Orchestra\Widget::make('pane'));

		$this->assertNotEquals(Orchestra\Widget::make('menu.a'), Orchestra\Widget::make('menu.b'));
	}
}
