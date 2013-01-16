<?php

Bundle::start('orchestra');

class MacroTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		URI::$uri = 'orchestra';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		URI::$uri = null;
	}
	
	/**
	 * Test HTML::title() macro.
	 *
	 * @test
	 */
	public function testHTMLTitleMacro()
	{
		$memory = Orchestra::memory();
		$memory->put('site.name', 'Orchestra');

		$this->assertEquals('Orchestra', HTML::title(null));
		$this->assertEquals('Home &mdash; Orchestra', HTML::title('Home'));

		$memory->put('site.format.title', ':page-title at :site-title');
		$this->assertEquals('Home at Orchestra', HTML::title('Home'));
	}
}