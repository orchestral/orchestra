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
		$this->assertFalse(Orchestra\Extension::started(DEFAULT_BUNDLE));
	}

	/**
	 * Test using extensions without any dependencies.
	 *
	 * @test
	 */
	public function testUsingExtensionWithoutAnyDependencies()
	{
		$this->restartApplication();

		Orchestra\Extension::detect();
		Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->assertTrue(Orchestra\Extension::started(DEFAULT_BUNDLE));
		$this->assertTrue(Orchestra\Extension::activated(DEFAULT_BUNDLE));

		$this->assertEquals('foobar', 
			Orchestra\Extension::option(DEFAULT_BUNDLE, 'foo'));

		Orchestra\Extension::deactivate(DEFAULT_BUNDLE);

		$this->assertFalse(Orchestra\Extension::activated(DEFAULT_BUNDLE));
	}

	/**
	 * Test extension unable to be activated when unresolved dependencies.
	 *
	 * @expectedException Orchestra\Extension\UnresolvedException
	 */
	public function testActivateExtensionFailedWhenUnresolvedDependencies()
	{
		$this->restartApplication();

		Orchestra\Extension::detect();
		Orchestra\Extension::activate('a');
	}

	/**
	 * Test extension unable to be deactivated when unresolved dependencies.
	 *
	 * @expectedException Orchestra\Extension\UnresolvedException
	 */
	public function testDeactivateExtensionFailedWhenUnresolvedDependencies()
	{
		$this->restartApplication();

		Orchestra\Extension::detect();
		Orchestra\Extension::activate('b');
		Orchestra\Extension::activate('a');	

		$this->assertTrue(Orchestra\Extension::started('a'));
		$this->assertTrue(Orchestra\Extension::activated('a'));
		$this->assertTrue(Orchestra\Extension::started('b'));
		$this->assertTrue(Orchestra\Extension::activated('b'));

		Orchestra\Extension::deactivate('b');
	}
}
