<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class HtmlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\HTML
	 */
	public function testInstanceOfHTML()
	{
		$this->assertInstanceOf('\Hybrid\HTML', new \Orchestra\HTML);
	}
}
