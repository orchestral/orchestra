<?php

Bundle::start('orchestra');

class RoutingPublisherTest extends Orchestra\Testable\TestCase {
	
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

		parent::tearDown();
	}

	/**
	 * Test Request GET (orchestra)/publisher without auth.
	 *
	 * @test
	 */
	public function testGetPublisherIndexPageWithoutAuth()
	{
		$response = $this->call('orchestra::publisher@index');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}
	
	/**
	 * Test Request GET (orchestra)/publisher
	 *
	 * @test
	 */
	public function testGetPublisherIndexPage()
	{
		$this->be($this->user);

		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'ftp');

		$response = $this->call('orchestra::publisher@index');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::publisher.ftp', $response->content->view);
	}
}