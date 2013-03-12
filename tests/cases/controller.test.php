<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class ControllerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Session::$instance = null;
		\Session::load();

		$_SERVER['test.orchestra.started'] = null;
		$_SERVER['test.orchestra.done']    = null;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Session::$instance = null;
		
		unset($_SERVER['test.orchestra.started']);
		unset($_SERVER['test.orchestra.done']);
	}

	/**
	 * Test Orchestra\Controller::__construct() triggers 
	 * `orchestra.started: backend`
	 *
	 * @test
	 * @group core
	 */
	public function testConstructController()
	{
		\Event::listen('orchestra.started: backend', function()
		{
			$_SERVER['test.orchestra.started'] = 'foo';
		});

		$this->assertNull($_SERVER['test.orchestra.started']);

		$controller = new \Orchestra\Controller;

		$this->assertEquals('foo', $_SERVER['test.orchestra.started']);
	}

	/**
	 * Test Orchestra\Controller::after() triggers `orchestra.done: backend`
	 *
	 * @test
	 * @group core
	 */
	public function testAfterFilter()
	{
		\Event::listen('orchestra.done: backend', function()
		{
			$_SERVER['test.orchestra.done'] = 'foo';
		});

		$this->assertNull($_SERVER['test.orchestra.done']);

		$controller = with(new \Orchestra\Controller)->after('foobar');

		$this->assertEquals('foo', $_SERVER['test.orchestra.done']);
	}
}
