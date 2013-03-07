<?php namespace Orchestra\Tests\Installer\Publisher;

\Bundle::start('orchestra');

class DirectoryTest extends \PHPUnit_Framework_TestCase {
	
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
		\File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory can be constructed.
	 *
	 * @test
	 * @group installer
	 */
	public function testConstructDirectory()
	{
		$this->assertInstanceOf('\Orchestra\Installer\Publisher\Directory', 
			new \Orchestra\Installer\Publisher\Directory);
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory::flush() method.
	 *
	 * @test
	 * @group installer
	 */
	public function testFlushMethod()
	{
		$directory = path('storage').'work'.DS.'publisher-directory-mock';
		$stub      = new \Orchestra\Installer\Publisher\Directory;

		$this->assertTrue($stub->flush($directory));

		\File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory::create() method 
	 * throw an exception.
	 *
	 * @expectedException \RuntimeException
	 */
	public function testCreateMethodThrowsException()
	{
		$directory = path('storage').'work'.DS.'publisher-directory-mock';
		$stub      = new \Orchestra\Installer\Publisher\Directory;
		
		$this->assertTrue($stub->flush($directory));
		$stub->create($directory);

		\File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}
}