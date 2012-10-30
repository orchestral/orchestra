<?php

class TestController extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the test
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
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
		$this->assertEquals(Orchestra\Core::memory(), \View::$shared['orchestra_memory']);
	}
}
