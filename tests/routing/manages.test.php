<?php

Bundle::start('orchestra');

class RoutingManagesTest extends Orchestra\Testable\TestCase {
	
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

		Event::listen('orchestra.manages: application.foo', function ()
		{
			return 'foobar';
		});

		$this->user = Orchestra\Model\User::find(1);

		$this->be($this->user);

		Orchestra\Extension::start(DEFAULT_BUNDLE);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

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

		$this->assertInstanceOf('Laravel\Response', $response);
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
		$response = $this->call('orchestra::manages@application.foobar');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.pages', $response->content->view);
		$this->assertFalse($response->content->data['content']);
	}
}