<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class HtmlTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\HTML
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfHTML()
	{
		$this->assertInstanceOf('\Orchestra\Support\HTML', new \Orchestra\HTML);
	}
}
