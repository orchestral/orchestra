<?php

Bundle::start('orchestra');

class ViewTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$_SERVER['view.started'] = null;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['view.started']);
	}

	/**
	 * Test instanceof Orchestra\View
	 *
	 * @test
	 */
	public function testBasicView()
	{
		Event::listen('orchestra.started: view', function ()
		{
			$_SERVER['view.started'] = 'foo';
		});

		$this->assertTrue(is_null($_SERVER['view.started']));

		$view = new Orchestra\View('home.index');

		$this->assertInstanceOf('Laravel\View', $view);
		$this->assertEquals('frontend', Orchestra\View::$theme);
		$this->assertEquals('foo', $_SERVER['view.started']);

		$refl = new \ReflectionObject($view);
		$file = $refl->getProperty('view');
		$file->setAccessible(true);

		$this->assertEquals('home.index', $file->getValue($view));
	}
}