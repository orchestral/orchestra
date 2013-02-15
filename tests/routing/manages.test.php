<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class ManagesTest extends \Orchestra\Testable\TestCase {
	
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
		
		\Event::listen('orchestra.manages: application.foo', function ()
		{
			return 'foobar';
		});

		\Orchestra\Extension::$extensions = array();
		\Orchestra\Extension::detect();

		$this->user = \Orchestra\Model\User::find(1);

		$this->be($this->user);

		\Orchestra\Extension::activate(DEFAULT_BUNDLE);
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

		\Orchestra\Extension::$extensions = array();
		
		parent::tearDown();
	}

	/**
	 * Test Request to manage foo
	 *
	 * @test
	 */
	public function testRequestToManageFoo()
	{
		$response = $this->call('orchestra::manages@application.foo');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.pages', $response->content->view);
		$this->assertEquals('foobar', $response->content->data['content']);

		$response = $this->call('orchestra::manages@foo');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.pages', $response->content->view);
		$this->assertEquals('foobar', $response->content->data['content']);

		$response = $this->call('orchestra::manages@application/foo');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.pages', $response->content->view);
		$this->assertEquals('foobar', $response->content->data['content']);
	}

	/**
	 * Test Request to manage invalid foobar
	 *
	 * @test
	 */
	public function testRequestToManageInvalidFoobar()
	{
		$response = $this->call('orchestra::manages@foobar');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
	}
}