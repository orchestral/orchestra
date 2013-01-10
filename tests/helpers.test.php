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
		$this->markTestIncomplete('Not completed.');
	}
}
