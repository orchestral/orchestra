<?php

class ControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
		$_SERVER['test.orchestra.started'] = null;
		$_SERVER['test.orchestra.done'] = null;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['test.orchestra.started']);
		unset($_SERVER['test.orchestra.done']);
	}

	/**
	 * Test Orchestra\Controller::__construct()
	 *
	 * @test
	 */
	public function testFilter()
	{
		$controller = new Orchestra\Controller;

		$this->assertTrue(\View::$shared['fluent_layout']);
		$this->assertEquals(Orchestra\Core::memory(),
			\View::$shared['orchestra_memory']);
	}

	/**
	 * Test Orchestra\Controller::__construct() triggers `orchestra.started: backend`
	 *
	 * @test
	 */
	public function testConstructTriggerEvents()
	{
		Event::listen('orchestra.started: backend', function()
		{
			$_SERVER['test.orchestra.started'] = 'foo';
		});

		$controller = new Orchestra\Controller;

		$this->assertEquals('foo', $_SERVER['test.orchestra.started']);
	}

	/**
	 * Test Orchestra\Controller::after() triggers `orchestra.done: backend`
	 *
	 * @test
	 */
	public function testAfterTriggerEvent()
	{

		Event::listen('orchestra.done: backend', function()
		{
			$_SERVER['test.orchestra.done'] = 'foo';
		});

		$controller = with(new Orchestra\Controller)->after('foobar');

		$this->assertEquals('foo', $_SERVER['test.orchestra.done']);
	}
}
