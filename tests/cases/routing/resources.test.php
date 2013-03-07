<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class ResourcesTest extends \Orchestra\Testable\TestCase {
	
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
	
		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		$this->stub = \Orchestra\Resources::make('foobar', array(
			'name' => 'Foobar',
			'uses' => 'stub',
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
		
		set_path('app', path('base').'application'.DS);
		set_path('orchestra.extension', path('bundle'));

		parent::tearDown();
	}
	
	/**
	 * Test Request GET (orchestra)/resources without auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResourcesIndexPageWithoutAuth()
	{
		$response = $this->call('orchestra::resources@index');

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/resources
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResourcesIndexPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::resources@index');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.index', $response->content->view);
	}

	/**
	 * Test Request GET (orchestra)/resources/foobar
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResourcesFoobarPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::resources@foobar');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.resources', $response->content->view);
	}
}