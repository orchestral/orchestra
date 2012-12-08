<?php

Bundle::start('orchestra');

class RoutingResourcesTest extends Orchestra\Testable\TestCase {
	
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

		$this->user = Orchestra\Model\User::find(1);
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
	 * Test Request GET (orchestra)/resources without auth
	 *
	 * @test
	 */
	public function testGetResourcesIndexPageWithoutAuth()
	{
		$response = $this->call('orchestra::resources@index');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/resources
	 *
	 * @test
	 */
	public function testGetResourcesIndexPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::resources@index');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::resources.index', $response->content->view);
	}
}