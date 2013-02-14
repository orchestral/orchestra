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

		$this->user = \Orchestra\Model\User::find(1);

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

		set_path('app', path('base').'application'.DS);
		set_path('orchestra.extension', path('bundle'));

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
	 * Test deactivated extension failed.
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
	 * Test deactivated extension failed.
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
	 * Test activate and deactivate extension successful.
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