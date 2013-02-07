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
			'username' => 'admin@orchestra.com',
			'password' => '123456',
		));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());
		$this->assertFalse(Auth::check());
	}

	/**
	 * Test Request POST (orchestra)/credential/login with validation errors.
	 * 
	 * @test
	 */
	public function testPostLoginPageWithValidationError()
	{
		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username' => 'admin+orchestra.com',
			Session::csrf_token => Session::token(),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
		$this->assertFalse(Auth::check());
	}

	/**
	 * Test Request POST (orchestra)/credential/login
	 * 
	 * @test
	 */
	public function testPostLoginPageIsSuccessful()
	{
		Event::listen('orchestra.auth: login', function ()
		{
			$_SERVER['orchestra.auth.login'] = 'foobar';
		});

		$this->assertEquals(null, $_SERVER['orchestra.auth.login']);

		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username'          => 'admin@orchestra.com',
			'password'          => '123456',
			Session::csrf_token => Session::token(),
		));

		
		$auth = Auth::user();

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(Auth::check());
		$this->assertEquals('admin@orchestra.com', $auth->email);
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.login']);
	}

	/**
	 * Test Request POST (orchestra)/credential/login failed when authentication
	 * is invalid.
	 * 
	 * @test
	 */
	public function testPostLoginPageWithInvalidAuthentication()
	{
		$response = $this->call('orchestra::credential@login', array(), 'POST', array(
			'username'          => 'admin@orchestra.com',
			'password'          => '1234561',
			Session::csrf_token => Session::token(),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$this->assertFalse(Auth::check());
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
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$this->assertFalse(Auth::check());
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.logout']);
	}


	/**
	 * Test Request GET (orchestra)/credential/register
	 * 
	 * @test
	 */
	public function testGetRegisterPage()
	{
		Orchestra\Core::memory()->put('site.users.registration', true);

		$response = $this->call('orchestra::credential@register', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::credential.register', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/credential/register failed without csrf.
	 * 
	 * @test
	 */
	public function testPostRegisterPageFailedWithoutCsrf()
	{
		Orchestra\Core::memory()->put('site.users.registration', true);
		
		$response = $this->call('orchestra::credential@register', array(), 'POST', array(
			'email'    => 'foobar@register-test.com',
			'fullname' => 'Test Register Foobar',
			'password' => '123456',
		));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());

		$user = Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertNull($user);
	}

	/**
	 * Test Request POST (orchestra)/credential/register with validation 
	 * error.
	 * 
	 * @test
	 */
	public function testPostRegisterPageWithValidationError()
	{
		unset(IoC::$registry['orchestra.user: register']);
		Orchestra\Core::memory()->put('site.users.registration', true);
		
		$response = $this->call('orchestra::credential@register', array(), 'POST', array(
			'email'             => 'foobar@register-test.com',
			'fullname'          => 'Test Register Foobar',
			'password'          => '123456',
			Session::csrf_token => Session::token(),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertFalse(is_null($user));
	}

	/**
	 * Test Request POST (orchestra)/credential/register
	 * 
	 * @test
	 */
	public function testPostRegisterPageIsSuccessful()
	{
		unset(IoC::$registry['orchestra.user: register']);
		Orchestra\Core::memory()->put('site.users.registration', true);
		
		$response = $this->call('orchestra::credential@register', array(), 'POST', array(
			'email'             => 'foobar@register-test.com',
			'fullname'          => 'Test Register Foobar',
			'password'          => '123456',
			Session::csrf_token => Session::token(),
		));

		$user = Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
		$this->assertInstanceOf('Orchestra\Model\User', 
			IoC::resolve('orchestra.user: register'));
		$this->assertFalse(is_null($user));
	}
}