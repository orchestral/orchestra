<?php

Bundle::start('orchestra');

class ExtensionTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		parent::tearDown();

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
		$this->createApplication();

		$memory = Orchestra\Core::memory();

		Orchestra\Extension::detect();
		Orchestra\Extension::start(DEFAULT_BUNDLE,
			$memory->get('extensions.available.'.DEFAULT_BUNDLE.'.config'));

		$this->assertTrue(Orchestra\Extension::started(DEFAULT_BUNDLE));
	}
}
