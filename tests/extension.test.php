<?php

Bundle::start('orchestra');

class ExtensionTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$_SERVER['extension.app.started'] = null;
		$_SERVER['extension.app.done']    = null;

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
	 * Test activate extensions without any dependencies.
	 *
	 * @test
	 */
	public function testActivateExtensionWithoutAnyDependencies()
	{
		$this->restartApplication();

		Event::listen('extension.started: '.DEFAULT_BUNDLE, function ()
		{
			$_SERVER['extension.app.started'] = 'foo';
		});

		Event::listen('extension.done: '.DEFAULT_BUNDLE, function ()
		{
			$_SERVER['extension.app.done'] = 'foo';
		});

		$this->assertTrue(is_null($_SERVER['extension.app.started']));

		Orchestra\Extension::detect();
		Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->assertEquals('foo', $_SERVER['extension.app.started']);

		$this->assertTrue(Orchestra\Extension::started(DEFAULT_BUNDLE));
		$this->assertTrue(Orchestra\Extension::activated(DEFAULT_BUNDLE));

		$this->assertEquals('foobar', 
			Orchestra\Extension::option(DEFAULT_BUNDLE, 'foo'));

		$this->assertTrue(is_null($_SERVER['extension.app.done']));

		Orchestra\Extension::shutdown();

		$this->assertEquals('foo', $_SERVER['extension.app.done']);
	}

	/**
	 * Test deactivate extensions without any dependencies.
	 *
	 * @test
	 */
	public function testDeactivateExtensionWithoutAnyDependencies()
	{
		$this->restartApplication();

		Orchestra\Extension::detect();
		Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->assertTrue(Orchestra\Extension::activated(DEFAULT_BUNDLE));

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
