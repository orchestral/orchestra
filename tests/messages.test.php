<?php

Bundle::start('orchestra');

class MessagesTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Session::$instance = null;
		Session::load();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Session::$instance = null;
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

		$serialize = $messages->serialize();

		$this->assertTrue(is_string($serialize));
		$this->assertContains('hello', $serialize);
		$this->assertContains('Hi World', $serialize);
		$this->assertContains('bye', $serialize);
		$this->assertContains('Goodbye', $serialize);

		Session::flash('message', $serialize);

		$this->assertTrue(Session::has('message'));

		$retrieve = Orchestra\Messages::retrieve();

		$this->assertEquals($messages, $retrieve);
		$this->assertEquals(array('Hi World'), $retrieve->get('hello'));
		$this->assertEquals(array('Goodbye'), $retrieve->get('bye'));
	}
}
