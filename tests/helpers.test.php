<?php

Bundle::start('orchestra');

class HelpersTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		URL::$base = null;
		
		parent::setUp();

		set_path('public', Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		set_path('public', path('base').'public'.DS);
	}

	/**
	 * Test handles() return proper URL
	 */
	public function testHandlesReturnProperURL()
	{
		$this->assertEquals('http://localhost/home', handles('home'));
		$this->assertEquals('http://localhost/home', handles('application::home'));
		$this->assertEquals('http://localhost/orchestra/login', handles('orchestra::login'));

	}

	/**
	 * Test memorize() return proper values
	 */
	public function testMemorizeReturnProperValues()
	{
		$this->assertEquals('Orchestra', memorize('site.name'));
		$this->assertEquals('foo', memorize('site.somefoo.value', 'foo'));
	}

	/**
	 * Test locate() return proper view path
	 */
	public function testLocateReturnProperViewPath()
	{
		$theme = Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS.'themes'.DS;
		$view1 = locate('path: /path/to/somewhere');
		$view2 = locate('home.index');
		$view3 = locate('error.404');

		$this->assertEquals('path: /path/to/somewhere', $view1);
		$this->assertEquals("path: {$theme}default/home/index.blade.php", $view2);
		$this->assertEquals('error.404', $view3);

	}
}
