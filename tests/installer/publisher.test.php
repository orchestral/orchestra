<?php

Bundle::start('orchestra');

class InstallerPublisherTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Session::$instance = null;
		Session::load();

		$_SERVER['bundle.published'] = array();

		IoC::register('task: orchestra.publisher', function($bundle)
		{
			$_SERVER['bundle.published'][] = $bundle;
		});

		set_path('public', Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Session::$instance = null;
		unset($_SERVER['bundle.published']);

		set_path('public', path('base').'public'.DS);
	}

	/**
	 * Test Orchestra\Installer\Publisher can be constructed.
	 *
	 * @test
	 */
	public function testConstructInstance()
	{
		Bundle::$bundles = array('a', 'b'); 
		$stub            = new Orchestra\Installer\Publisher;

		$this->assertInstanceOf('Orchestra\Installer\Publisher', $stub);

		$this->assertTrue($stub->publish());
		$this->assertTrue(Bundle::$bundles, $_SERVER['bundle.published']);
	}
}