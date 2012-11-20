<?php

class MessagesTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Session::load();

		Bundle::start('orchestra');
	}

	/**
	 * Test Orchestra\Messages::make()
	 *
	 * @test
	 */
	public function testMake()
	{
		$messages = Orchestra\Messages::make('welcome', 'Hello world');

		$this->assertInstanceOf('Orchestra\Messages', $messages);
		$this->assertEquals(array('Hello world'), $messages->get('welcome'));

		$messages->add('welcome', 'Hi Foobar');
		$this->assertEquals(array('Hello world', 'Hi Foobar'), $messages->get('welcome'));
	}

	/**
	 * Test serializing and retrieving Orchestra\Messages over
	 * Session
	 *
	 * @test
	 */
	public function testSerializeAndRetrieve()
	{
		$messages = new Orchestra\Messages;
		$messages->add('hello', 'Hi World');
		$messages->add('bye', 'Goodbye');

		Session::flash('message', $messages->serialize());

		$this->assertTrue(Session::has('message'));

		$this->assertEquals($messages, Orchestra\Messages::retrieve());
	}
}
