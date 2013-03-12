<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class MessagesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Session::$instance = null;
		\Session::load();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Session::$instance = null;
	}

	/**
	 * Test Orchestra\Messages::make()
	 *
	 * @test
	 * @group support
	 */
	public function testMakeInstance()
	{
		$messages = \Orchestra\Support\Messages::make();
		$messages->add('welcome', 'Hello world');

		$this->assertInstanceOf('\Orchestra\Support\Messages', $messages);
		$this->assertEquals(array('Hello world'), $messages->get('welcome'));

		$messages->add('welcome', 'Hi Foobar');
		$this->assertEquals(array('Hello world', 'Hi Foobar'), $messages->get('welcome'));
	}

	/**
	 * Test serializing and retrieving Orchestra\Messages over
	 * Session
	 *
	 * @test
	 * @group support
	 */
	public function testUsingMessages()
	{
		$messages = new \Orchestra\Support\Messages;
		$messages->add('hello', 'Hi World');
		$messages->add('bye', 'Goodbye');

		$serialize = $messages->serialize();

		$this->assertTrue(is_string($serialize));
		$this->assertContains('hello', $serialize);
		$this->assertContains('Hi World', $serialize);
		$this->assertContains('bye', $serialize);
		$this->assertContains('Goodbye', $serialize);

		$messages->save();

		$this->assertTrue(\Session::has('message'));

		$retrieve = \Orchestra\Support\Messages::retrieve();

		$this->assertInstanceOf('\Orchestra\Support\Messages', $retrieve);
		$this->assertEquals($messages, $retrieve);
		$this->assertEquals(array('Hi World'), $retrieve->get('hello'));
		$this->assertEquals(array('Goodbye'), $retrieve->get('bye'));
	}
}
