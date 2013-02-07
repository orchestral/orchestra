<?php

Bundle::start('orchestra');

class InstallerPublisherDirectoryTest extends PHPUnit_Framework_TestCase {
	
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
		File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory can be constructed.
	 *
	 * @test
	 */
	public function testConstructDirectory()
	{
		$stub = new Orchestra\Installer\Publisher\Directory;

		$this->assertInstanceOf('Orchestra\Installer\Publisher\Directory', $stub);
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory::flush() method.
	 *
	 * @test
	 */
	public function testFlushMethod()
	{
		$directory = path('storage').'work'.DS.'publisher-directory-mock';
		$stub      = new Orchestra\Installer\Publisher\Directory;

		$this->assertTrue($stub->flush($directory));

		File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}

	/**
	 * Test Orchestra\Installer\Publisher\Directory::create() method 
	 * throw an exception.
	 *
	 * @expectedException RuntimeException
	 */
	public function testCreateMethodThrowsException()
	{
		$directory = path('storage').'work'.DS.'publisher-directory-mock';
		$stub      = new Orchestra\Installer\Publisher\Directory;
		
		$this->assertTrue($stub->flush($directory));
		$stub->create($directory);

		File::rmdir(path('storage').'work'.DS.'publisher-directory-mock');
	}
}