<?php

require_once "_utils/setup_testcase.php";

class ExtensionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$base_path =  Bundle::path('orchestra').'tests'.DS.'_utils'.DS;
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);

		Bundle::start('orchestra');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		set_path('app', path('base').'application'.DS);
		set_path('orchestra.extension', path('bundle'));
	}

	/**
	 * Test invalid extension is not started.
	 *
	 * @test
	 */
	public function testInvalidExtensionIsNotStarted()
	{
		$this->assertFalse(Orchestra\Extension::started('unknownfoo'));
	}

	/**
	 * Test detect extensions.
	 *
	 * @test
	 */
	public function testDetectExtension()
	{
		setup_orchestra_fixture();
	}
}
