<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class ExtensionsTest extends \Orchestra\Testable\TestCase {
	
	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	private $user = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);

		$this->user = \Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

		$base_path = path('base');
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);

		parent::tearDown();
	}
	
	/**
	 * Test Request GET (orchestra)/extensions
	 *
	 * @test
	 * @group routing
	 */
	public function testGetExtensionIndexPage()
	{
		$this->be($this->user);
		$this->call('orchestra::extensions@index');
		$this->assertViewIs('orchestra::extensions.index');
	}

	/**
	 * Test activate extension failed.
	 *
	 * @test
	 * @group routing
	 */
	public function testActivateExtensionFailed()
	{
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@activate');
		$this->assertResponseNotFound();

		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->call('orchestra::extensions@activate', array(DEFAULT_BUNDLE));
		$this->assertResponseNotFound();

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
	}

	/**
	 * Test activate extension failed with dependencies error.
	 *
	 * @test
	 * @group routing
	 */
	public function testActivateExtensionFailedDependenciesError()
	{
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@activate', array('a'));
		$this->assertRedirectedTo(handles('orchestra::extensions'));
	}

	/**
	 * Test activate extension failed with publisher error.
	 *
	 * @test
	 * @group routing
	 */
	public function testActivateExtensionFailedPublisherError()
	{
		$this->restartApplication();

		$events = \Event::$events;
		\Event::listen('orchestra.publishing: extension', function ($name)
		{
			throw new \Orchestra\Extension\FilePermissionException();
		});

		$this->be($this->user);
		$this->call('orchestra::extensions@activate', array('e'));
		$this->assertRedirectedTo(handles('orchestra::publisher'));

		\Event::$events = $events;
	}

	/**
	 * Test de-activate extension failed.
	 *
	 * @test
	 * @group routing
	 */
	public function testDeactivateExtensionFailed()
	{
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@deactivate');
		$this->assertResponseNotFound();

		$this->call('orchestra::extensions@deactivate', array(DEFAULT_BUNDLE));
		$this->assertResponseNotFound();
	}

	/**
	 * Test de-activate extension failed with dependencies error.
	 *
	 * @test
	 * @group routing
	 */
	public function testDeactivateExtensionFailedDependenciesError()
	{
		$this->restartApplication();

		\Bundle::register('aws', array(
			'location' => "path: ".path('orchestra.extension').'aws'.DS,
		));

		\Bundle::start('aws');
		\Orchestra\Extension::activate('b');
		\Orchestra\Extension::activate('a');

		$this->be($this->user);
		$this->call('orchestra::extensions@deactivate', array('b'));
		$this->assertRedirectedTo(handles('orchestra::extensions'));
		$this->assertTrue(\Orchestra\Extension::activated('b'));
		$this->assertTrue(\Orchestra\Extension::activated('a'));

		\Orchestra\Extension::deactivate('a');
		\Orchestra\Extension::deactivate('b');
	}

	/**
	 * Test activate and de-activate extension successful.
	 *
	 * @test
	 * @group routing
	 */
	public function testActivateAndDeactivateExtensionSuccessful()
	{
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@activate', array(DEFAULT_BUNDLE));
		$this->assertRedirectedTo(handles('orchestra::extensions'));
		$this->assertTrue(\Orchestra\Extension::activated(DEFAULT_BUNDLE));

		$this->call('orchestra::extensions@deactivate', array(DEFAULT_BUNDLE));
		$this->assertRedirectedTo(handles('orchestra::extensions'));
		$this->assertFalse(\Orchestra\Extension::activated(DEFAULT_BUNDLE));
	}

	/**
	 * Test get extension configuration successful.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetConfigureExtensionSuccessful()
	{
		\Orchestra\Extension::activate('e');
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@configure', array('e'));
		$this->assertViewIs('orchestra::extensions.configure');

		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Test post extension configuration successful.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostConfigureExtensionSuccessful()
	{
		\Orchestra\Extension::activate('e');
		$this->restartApplication();

		$this->be($this->user);

		$this->call('orchestra::extensions@configure', array('e'), 'POST', array(
			'handles' => 'change-handles',
		));

		$memory = \Orchestra\Core::memory();

		$this->assertRedirectedTo(handles('orchestra::extensions'));
		$this->assertEquals('change-handles', $memory->get('extensions.active.e.handles'));

		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Test update extension successful.
	 *
	 * @test
	 * @group routing
	 */
	public function testUpdateExtensionSuccessful()
	{
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@update', array(DEFAULT_BUNDLE));
		$this->assertRedirectedTo(handles('orchestra::extensions'));

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
	}

	/**
	 * Test update extension failed when extension is not started.
	 *
	 * @test
	 * @group routing
	 */
	public function testUpdateExtensionFail()
	{
		$this->restartApplication();

		$this->be($this->user);
		$this->call('orchestra::extensions@update', array(DEFAULT_BUNDLE));
		$this->assertResponseNotFound();
	}

	/**
	 * Test activate extension failed with publisher error.
	 *
	 * @test
	 * @group routing
	 */
	public function testUpdateExtensionFailedPublisherError()
	{
		$this->restartApplication();

		\Orchestra\Extension::activate('e');

		$events = \Event::$events;
		\Event::listen('orchestra.publishing: extension', function ($name)
		{
			throw new \Orchestra\Extension\FilePermissionException();
		});

		$this->be($this->user);
		$this->call('orchestra::extensions@update', array('e'));
		$this->assertRedirectedTo(handles('orchestra::publisher'));

		\Event::$events = $events;
		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Run Orchestra Platform in safe mode.
	 *
	 * @test
	 * @group routing
	 */
	public function testRunningInSafeMode()
	{
		$_GET['safe_mode'] = '1';

		$this->restartApplication();

		$this->call('orchestra::credential@login', array(), 'GET');
		$this->assertEquals('Y', \Session::get('safe_mode'));

		$_GET['safe_mode'] = '0';

		$this->restartApplication();

		$this->call('orchestra::credential@login', array(), 'GET');
		$this->assertNull(\Session::get('safe_mode'));
	}
}