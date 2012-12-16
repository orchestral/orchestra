<?php

Bundle::start('orchestra');

class HtmlTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\HTML
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Hybrid\HTML', new Orchestra\HTML);
	}
}
