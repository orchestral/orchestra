<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class ExtensionTest extends \Orchestra\Testable\TestCase {

	/**
	 * Fixture path.
	 *
	 * @var string
	 */
	protected $base_path = '';

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$_SERVER['extension.app.started'] = null;
		$_SERVER['extension.app.done']    = null;

		$this->base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $this->base_path.'application'.DS);
		set_path('orchestra.extension', $this->base_path.'bundles'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['extension.app.started']);
		unset($_SERVER['extension.app.done']);

		set_path('app', path('base').'application'.DS);
		set_path('orchestra.extension', path('bundle'));

		parent::tearDown();
	}

	/**
	 * Test invalid extension is not started.
	 *
	 * @test
	 * @group core
	 * @group extension
	 */
	public function testInvalidExtensionIsNotStarted()
	{
		$this->assertFalse(\Orchestra\Extension::started('unknownfoo'));
		$this->assertFalse(\Orchestra\Extension::started(DEFAULT_BUNDLE));

		$this->assertEquals('invalid', 
			\Orchestra\Extension::option('unknownfoo', 'foo', 'invalid'));
	}

	/**
	 * Test activate extensions without any dependencies.
	 *
	 * @test
	 * @group core
	 * @group extension
	 */
	public function testActivateExtensionWithoutAnyDependencies()
	{
		$this->restartApplication();

		\Event::listen('extension.started: '.DEFAULT_BUNDLE, function ()
		{
			$_SERVER['extension.app.started'] = 'foo';
		});

		\Event::listen('extension.done: '.DEFAULT_BUNDLE, function ()
		{
			$_SERVER['extension.app.done'] = 'foo';
		});

		$this->assertNull($_SERVER['extension.app.started']);

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->assertTrue(is_bool(\Orchestra\Extension::available(DEFAULT_BUNDLE)));
		$this->assertEquals('foo', $_SERVER['extension.app.started']);

		$this->assertTrue(\Orchestra\Extension::started(DEFAULT_BUNDLE));
		$this->assertTrue(\Orchestra\Extension::activated(DEFAULT_BUNDLE));

		$this->assertNull($_SERVER['extension.app.done']);

		\Orchestra\Extension::shutdown();

		$this->assertEquals('foo', $_SERVER['extension.app.done']);
	}

	/**
	 * Test deactivate extensions without any dependencies.
	 *
	 * @test
	 * @group core
	 * @group extension
	 */
	public function testDeactivateExtensionWithoutAnyDependencies()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->assertTrue(\Orchestra\Extension::activated(DEFAULT_BUNDLE));

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);

		$this->assertFalse(\Orchestra\Extension::activated(DEFAULT_BUNDLE));
	}

	/**
	 * Test extension unable to be activated when unresolved dependencies.
	 *
	 * @expectedException \Orchestra\Extension\UnresolvedException
	 * @group core
	 * @group extension
	 */
	public function testActivateExtensionFailedWhenUnresolvedDependencies()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate('a');
	}

	/**
	 * Test extension unable to be activated when unresolved dependencies
	 * due to version.
	 *
	 * @expectedException \Orchestra\Extension\UnresolvedException
	 * @group core
	 * @group extension
	 */
	public function testActivateExtensionFailedWhenUnresolvedDependenciesByVersion()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate('d');
		\Orchestra\Extension::activate('c');
	}

	/**
	 * Test extension unable to be detect extension when json can't be 
	 * parsed.
	 *
	 * @expectedException \RuntimeException
	 * @group core
	 * @group extension
	 */
	public function testDetectExtensionCauseThrowsExceptionWithoutValidJson()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect(array(
			'invalidbundle' => $this->base_path.'invalid-bundle'.DS,
		));
	}

	/**
	 * Test Orchestra\Extension::option() method.
	 *
	 * @test
	 * @group core
	 * @group extension
	 */
	public function testOptionMethod()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate('e');	

		$this->assertEquals('foobar', \Orchestra\Extension::option('e', 'foo'));
	}

	/**
	 * Test extension unable to be deactivated when unresolved dependencies.
	 *
	 * @expectedException \Orchestra\Extension\UnresolvedException
	 * @group core
	 * @group extension
	 */
	public function testDeactivateExtensionFailedWhenUnresolvedDependencies()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();

		\Bundle::register('aws');
		\Bundle::start('aws');

		\Orchestra\Extension::activate('b');
		\Orchestra\Extension::activate('a');

		\Orchestra\Core::shutdown();
		\Orchestra\Core::start();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::deactivate('b');
	}

	/**
	 * Test extension unable to be activated when unresolved dependencies
	 * due to unavailable extension.
	 *
	 * @test
	 * @group core
	 * @group extension
	 */
	public function testDeactivateExtensionFailedWhenUnresolvedDependenciesDueToUnavailableExtension()
	{
		$this->restartApplication();

		\Orchestra\Extension::detect();
		
		$results  = \Orchestra\Extension::unresolved('f', false);
		$expected = array(
			array(
				'name'    => 'some-unknown-and-invalid-extension',
				'version' => '>0.1.0',
			),
		);

		$this->assertTrue(is_array($results));
		$this->assertEquals($expected, $results);
	}
}
