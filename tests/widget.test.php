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
		$this->assertInstanceOf('Orchestra\Widget\Menu',
			Orchestra\Widget::make('menu'));
		$this->assertInstanceOf('Orchestra\Widget\Pane',
			Orchestra\Widget::make('pane'));
		$this->assertInstanceOf('Orchestra\Widget\Placeholder',
			Orchestra\Widget::make('placeholder'));
	}

	/**
	 * Test Orchestra\Widget::make() with different name return different
	 * instance.
	 *
	 * @test
	 */
	public function testMakeDifferentNameReturnDifferentInstance()
	{
		$this->assertNotEquals(Orchestra\Widget::make('menu.a'),
			Orchestra\Widget::make('menu.b'));
	}

	/**
	 * Test Orchestra\Widget::make() with the same name return the same
	 * instance.
	 *
	 * @test
	 */
	public function testMakeSameNameReturnSameInstance()
	{
		$this->assertEquals(Orchestra\Widget::make('menu.a'),
			Orchestra\Widget::make('menu.a'));
	}

	/**
	 * Test Orchestra\Widget::make() with an invalid driver throw an
	 * exception
	 *
	 * @expectedException \Exception
	 */
	public function testMakeWithInvalidDriverThrowException()
	{
		Orchestra\Widget::make('menus');
	}
}
