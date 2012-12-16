<?php

Bundle::start('orchestra');

class RoutingSettingsTest extends Orchestra\Testable\TestCase {
	
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
	 * Test Request GET (orchestra)/settings without auth
	 *
	 * @test
	 */
	public function testGetSettingsPageWithoutAuth()
	{
		$response = $this->call('orchestra::settings@index');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/settings
	 *
	 * @test
	 */
	public function testGetSettingsPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::settings@index');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::settings.index', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/setting
	 *
	 * @test
	 */
	public function testPostSettingsPage()
	{
		$this->markTestIncomplete("Not completed.");
	}
}