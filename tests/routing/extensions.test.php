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
	 */
	public function testGetExtensionIndexPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::extensions@index');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::extensions.index', $response->content->view);
	}

	/**
	 * Test activate extension failed.
	 *
	 * @test
	 */
	public function testActivateExtensionFailed()
	{
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@activate');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
		$this->assertEquals('error.404', $response->content->view);

		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$response = $this->call('orchestra::extensions@activate', array(DEFAULT_BUNDLE));
		
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
		$this->assertEquals('error.404', $response->content->view);

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
	}

	/**
	 * Test activate extension failed with dependencies error.
	 *
	 * @test
	 */
	public function testActivateExtensionFailedDependenciesError()
	{
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@activate', array('a'));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test activate extension failed with publisher error.
	 *
	 * @test
	 */
	public function testActivateExtensionFailedPublisherError()
	{
		$this->restartApplication();

		$this->be($this->user);

		$events = \Event::$events;
		\Event::listen('orchestra.publishing: extension', function ($name)
		{
			throw new \Orchestra\Extension\FilePermissionException();
		});

		$response = $this->call('orchestra::extensions@activate', array('e'));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::publisher'), 
			$response->foundation->headers->get('location'));

		\Event::$events = $events;
	}

	/**
	 * Test de-activate extension failed.
	 *
	 * @test
	 */
	public function testDeactivateExtensionFailed()
	{
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@deactivate');
		
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
		$this->assertEquals('error.404', $response->content->view);

		$response = $this->call('orchestra::extensions@deactivate', array(DEFAULT_BUNDLE));
		
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
		$this->assertEquals('error.404', $response->content->view);
	}

	/**
	 * Test de-activate extension failed with dependencies error.
	 *
	 * @test
	 */
	public function testDeactivateExtensionFailedDependenciesError()
	{
		$this->restartApplication();

		$this->be($this->user);

		\Bundle::register('aws', array(
			'location' => "path: ".path('orchestra.extension').'aws'.DS,
		));

		\Bundle::start('aws');
		\Orchestra\Extension::activate('b');
		\Orchestra\Extension::activate('a');

		$response = $this->call('orchestra::extensions@deactivate', array('b'));
		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));

		$this->assertTrue(\Orchestra\Extension::activated('b'));
		$this->assertTrue(\Orchestra\Extension::activated('a'));

		\Orchestra\Extension::deactivate('a');
		\Orchestra\Extension::deactivate('b');
	}

	/**
	 * Test activate and de-activate extension successful.
	 *
	 * @test
	 */
	public function testActivateAndDeactivateExtensionSuccessful()
	{
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@activate', array(DEFAULT_BUNDLE));
		
		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));

		$this->assertTrue(\Orchestra\Extension::activated(DEFAULT_BUNDLE));

		$response = $this->call('orchestra::extensions@deactivate', array(DEFAULT_BUNDLE));
		
		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));

		$this->assertFalse(\Orchestra\Extension::activated(DEFAULT_BUNDLE));
	}

	/**
	 * Test get extension configuration successful.
	 *
	 * @test
	 */
	public function testGetConfigureExtensionSuccessful()
	{
		\Orchestra\Extension::activate('e');
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@configure', array('e'));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::extensions.configure', $response->content->view);

		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Test post extension configuration successful.
	 *
	 * @test
	 */
	public function testPostConfigureExtensionSuccessful()
	{
		\Orchestra\Extension::activate('e');
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@configure', array('e'), 'POST', array(
			'handles' => 'change-handles',
		));

		$memory = \Orchestra\Core::memory();

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));
		$this->assertEquals('change-handles', 
			$memory->get('extensions.active.e.handles'));

		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Test update extension successful.
	 *
	 * @test
	 */
	public function testUpdateExtensionSuccessful()
	{
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@update', array(DEFAULT_BUNDLE));
		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::extensions'), 
			$response->foundation->headers->get('location'));

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
	}

	/**
	 * Test update extension failed when extension is not started.
	 *
	 * @test
	 */
	public function testUpdateExtensionFail()
	{
		$this->restartApplication();

		$this->be($this->user);

		$response = $this->call('orchestra::extensions@update', array(DEFAULT_BUNDLE));
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
	}

	/**
	 * Test activate extension failed with publisher error.
	 *
	 * @test
	 */
	public function testUpdateExtensionFailedPublisherError()
	{
		$this->restartApplication();

		\Orchestra\Extension::activate('e');

		$this->be($this->user);

		$events = \Event::$events;
		\Event::listen('orchestra.publishing: extension', function ($name)
		{
			throw new \Orchestra\Extension\FilePermissionException();
		});

		$response = $this->call('orchestra::extensions@update', array('e'));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::publisher'), 
			$response->foundation->headers->get('location'));

		\Event::$events = $events;
		\Orchestra\Extension::deactivate('e');
	}

	/**
	 * Run Orchestra Platform in safe mode.
	 *
	 * @test
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