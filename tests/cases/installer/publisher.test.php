<?php namespace Orchestra\Tests\Installer;

\Bundle::start('orchestra');

class PublisherTest extends \PHPUnit_Framework_TestCase {
	
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
	 * Test Orchestra\Installer\Publisher can be constructed.
	 *
	 * @test
	 * @group installer
	 */
	public function testConstructPublisher()
	{
		$stub = new \Orchestra\Installer\Publisher;

		$this->assertInstanceOf('\Orchestra\Installer\Publisher', $stub);
	}

	/**
	 * Test Orchestra\Installer\Publisher::publish() method.
	 *
	 * @test
	 * @group installer
	 */
	public function testPublishMethod()
	{
		\File::mkdir(path('storage').'work'.DS.'publisher-mock', 777);

		$mock = $this->getMock('\Orchestra\Installer\Publisher\Directory', array('flush'));
		$mock->expects($this->any())
			->method('flush')
			->will($this->returnValue(true));
		
		$stub = new \Orchestra\Installer\Publisher;
		$this->assertTrue($stub->publish());

		\File::rmdir(path('storage').'work'.DS.'publisher-mock');
	}
}