<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class MessagesTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test instanceof Orchestra\Support\Messages.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfMessages()
	{
		$stub = new \Orchestra\Messages;
		$this->assertInstanceOf('\Orchestra\Support\Messages', $stub);
	}
}
