<?php

Bundle::start('orchestra');

class RoutingCredentialTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$_SERVER['orchestra.auth.login']  = null;
		$_SERVER['orchestra.auth.logout'] = null;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		parent::tearDown();
		
		unset($_SERVER['orchestra.auth.login']);
		unset($_SERVER['orchestra.auth.logout']);
	}

	/**
	 * Test Request GET (orchestra)/credential/login
	 * 
	 * @test
	 */
	public function testGetLoginPage()
	{
		$response = $this->call('orchestra::credential@login', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::credential.login', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/credential/login failed without csrf.
	 * 
	 * @test
	 */
	public function testPostLoginPageFailedWithoutCsrf()
	{
		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username' => 'example@test.com',
			'password' => '123456',
		));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());

		$this->assertFalse(Auth::check());
	}

	/**
	 * Test Request POST (orchestra)/credential/login
	 * 
	 * @test
	 */
	public function testPostLoginPage()
	{
		Event::listen('orchestra.auth: login', function ()
		{
			$_SERVER['orchestra.auth.login'] = 'foobar';
		});

		$this->assertEquals(null, $_SERVER['orchestra.auth.login']);

		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username'          => 'example@test.com',
			'password'          => '123456',
			Session::csrf_token => Session::token(),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(handles('orchestra'), 
			$response->foundation->headers->get('location'));

		$this->assertTrue(Auth::check());
		$this->assertEquals(Auth::user(), Orchestra\Model\User::find(1));
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.login']);
	}

	/**
	 * Test Request GET (orchestra)/credential/login
	 * 
	 * @test
	 */
	public function testGetLogoutPage()
	{
		$this->assertEquals(null, $_SERVER['orchestra.auth.logout']);

		Event::listen('orchestra.auth: logout', function ()
		{
			$_SERVER['orchestra.auth.logout'] = 'foobar';
		});

		$response = $this->call('orchestra::credential@logout', array());

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$this->assertFalse(Auth::check());
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.logout']);
	}

	/**
	 * Test Request POST (orchestra)/credential/login
	 * 
	 * @test
	 */
	public function testPostLoginPageWithInvalidResponse()
	{
		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username'          => 'example@test.com',
			'password'          => '1234561',
			Session::csrf_token => Session::token(),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$this->assertFalse(Auth::check());
	}
}