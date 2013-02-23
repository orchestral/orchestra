<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class ViewTest extends \PHPUnit_Framework_TestCase {
	
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
	 * Test construct a Orchestra\View
	 *
	 * @test
	 * @group core
	 */
	public function testConstructView()
	{
		\Event::listen('orchestra.started: view', function ()
		{
			$_SERVER['view.started'] = 'foo';
		});

		$this->assertNull($_SERVER['view.started']);

		$view = new \Orchestra\View('orchestra::layout.main');

		$this->assertInstanceOf('\Laravel\View', $view);
		$this->assertEquals('frontend', \Orchestra\View::$theme);
		$this->assertEquals('foo', $_SERVER['view.started']);

		$refl = new \ReflectionObject($view);
		$file = $refl->getProperty('view');
		$file->setAccessible(true);

		$this->assertEquals('orchestra::layout.main', $file->getValue($view));
	}

	/**
	 * Test Orchestra\View::exists() method.
	 *
	 * @test
	 * @group core
	 */
	public function testExistsMethod()
	{
		\Orchestra\View::name('orchestra::layout.main', 'layout');

		$path     = \Orchestra\View::exists('orchestra::layout.main', true);
		$expected = \Bundle::path('orchestra').'views'.DS.'layout'.DS.'main.blade.php';

		$this->assertTrue(is_bool(\Orchestra\View::exists('orchestra::layout.main')));
		$this->assertTrue(is_bool(\Orchestra\View::exists('name: layout')));
		$this->assertTrue(is_bool(\Orchestra\View::exists("path: {$expected}")));
		$this->assertEquals($expected, $path);
	}
}