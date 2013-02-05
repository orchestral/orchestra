<?php

Bundle::start('orchestra');

class RoutingInstallerTest extends Orchestra\Testable\TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->removeApplication();

		Session::load();

		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		set_path('app', path('base').'application'.DS);
		parent::tearDown();
	}

	/**
	 * Test Request GET (orchestra)/installer/index
	 *
	 * @test
	 */
	public function testGetInstallerPage()
	{
		$response = $this->call('orchestra::installer@index', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.index', $response->content->view);

		$response = $this->call('orchestra::installer@steps', array(1));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.step1', $response->content->view);

		$response = $this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin+orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::installer/steps/1'), 
			$response->foundation->headers->get('location'));

		$response = $this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin@orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.step2', $response->content->view);
	}
}