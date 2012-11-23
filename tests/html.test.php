<?php

class HtmlTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test instanceof Orchestra\HTML
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Hybrid\HTML', new Orchestra\HTML);
	}
}
