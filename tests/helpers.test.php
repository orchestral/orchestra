<?php

class HelpersTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		URL::$base = null;
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', '');

		Bundle::start('orchestra');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}

	/**
	 * Test handles() return proper URL
	 */
	public function testHandlesReturnProperURL()
	{
		$expected = 'http://localhost/home';
		$this->assertEquals($expected, handles('home'));
	}
}
