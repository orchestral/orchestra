<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class CredentialTest extends \Orchestra\Testable\TestCase {

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
		unset($_SERVER['orchestra.auth.login']);
		unset($_SERVER['orchestra.auth.logout']);

		parent::tearDown();
	}

	/**
	 * Get Mailer Mock
	 *
	 * @test
	 * @group routing
	 */
	public function getMailerMock()
	{
		$mock = $this->getMockBuilder('\Orchestra\Testable\Mailer')
					->disableOriginalConstructor()
					->setMethods(array('was_sent'))
					->getMock();

		$mock->expects($this->any())
			->method('was_sent')
			->will($this->returnValue(false));

		\IoC::register('orchestra.mailer', function ($from = true) use ($mock)
		{
			return new $mock(array());
		});
	}

	/**
	 * Test Request GET (orchestra)/credential/login
	 * 
	 * @test
	 * @group routing
	 */
	public function testGetLoginPage()
	{
		$this->call('orchestra::credential@login', array());
		$this->assertViewIs('orchestra::credential.login');
	}

	/**
	 * Test Request POST (orchestra)/credential/login failed without csrf.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostLoginPageFailedWithoutCsrf()
	{
		$post = array(
			'username' => 'admin@orchestra.com',
			'password' => '123456',
		);
		
		$this->call('orchestra::credential@login', array(), 'POST', $post);
		$this->assertResponseIs(500);
		$this->assertFalse(\Auth::check());
	}

	/**
	 * Test Request POST (orchestra)/credential/login with validation errors.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostLoginPageWithValidationError()
	{
		$post = array(
			'username'           => 'admin+orchestra.com',
			\Session::csrf_token => \Session::token(),
		);
		
		$this->call('orchestra::credential@login', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::login'));
		$this->assertFalse(\Auth::check());
	}

	/**
	 * Test Request POST (orchestra)/credential/login
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostLoginPageIsSuccessful()
	{
		$post = array(
			'username'           => 'admin@orchestra.com',
			'password'           => '123456',
			\Session::csrf_token => \Session::token(),
		);

		\Event::listen('orchestra.auth: login', function ()
		{
			$_SERVER['orchestra.auth.login'] = 'foobar';
		});

		$this->assertEquals(null, $_SERVER['orchestra.auth.login']);

		$this->call('orchestra::credential@login', array(), 'POST', $post);
		
		$auth = \Auth::user();

		$this->assertRedirectedTo(handles('orchestra'));
		$this->assertTrue(\Auth::check());
		$this->assertEquals('admin@orchestra.com', $auth->email);
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.login']);
	}

	/**
	 * Test Request POST (orchestra)/credential/login failed when authentication
	 * is invalid.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostLoginPageWithInvalidAuthentication()
	{
		$post = array(
			'username'           => 'admin@orchestra.com',
			'password'           => '1234561',
			\Session::csrf_token => \Session::token(),
		);

		$this->call('orchestra::credential@login', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::login'));
		$this->assertFalse(\Auth::check());
	}

	/**
	 * Test Request GET (orchestra)/credential/login
	 * 
	 * @test
	 * @group routing
	 */
	public function testGetLogoutPage()
	{
		$this->assertEquals(null, $_SERVER['orchestra.auth.logout']);

		\Event::listen('orchestra.auth: logout', function ()
		{
			$_SERVER['orchestra.auth.logout'] = 'foobar';
		});

		$this->call('orchestra::credential@logout', array());
		$this->assertRedirectedTo(handles('orchestra::login'));
		$this->assertFalse(\Auth::check());
		$this->assertEquals('foobar', $_SERVER['orchestra.auth.logout']);
	}


	/**
	 * Test Request GET (orchestra)/credential/register
	 * 
	 * @test
	 * @group routing
	 */
	public function testGetRegisterPage()
	{
		\Orchestra\Core::memory()->put('site.users.registration', true);

		$this->call('orchestra::credential@register', array());
		$this->assertViewIs('orchestra::credential.register');
	}

	/**
	 * Test Request POST (orchestra)/credential/register failed without csrf.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostRegisterPageFailedWithoutCsrf()
	{
		$post = array(
			'email'    => 'foobar@register-test.com',
			'fullname' => 'Test Register Foobar',
			'password' => '123456',
		);

		\Orchestra\Core::memory()->put('site.users.registration', true);
		
		$this->call('orchestra::credential@register', array(), 'POST', $post);

		$user = \Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertResponseIs(500);
		$this->assertNull($user);
	}

	/**
	 * Test Request POST (orchestra)/credential/register with validation 
	 * error.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostRegisterPageWithValidationError()
	{
		$post = array(
			'email'              => 'foobar+register-test.com',
			'fullname'           => 'Test Register Foobar',
			'password'           => '123456',
			\Session::csrf_token => \Session::token(),
		);

		unset(\IoC::$registry['orchestra.user: register']);
		\Orchestra\Core::memory()->put('site.users.registration', true);
		
		$this->call('orchestra::credential@register', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::register'));
	}

	/**
	 * Test Request POST (orchestra)/credential/register with database error.
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostRegisterPageWithDatabaseError()
	{
		$post = array(
			'email'              => 'foobar@register-test.com',
			'fullname'           => 'Test Register Foobar',
			'password'           => '123456',
			\Session::csrf_token => \Session::token(),
		);

		unset(\IoC::$registry['orchestra.user: register']);
		\Orchestra\Core::memory()->put('site.users.registration', true);

		$events = \Event::$events;
		\Event::listen('eloquent.saving: Orchestra\Model\User', function($model)
		{
			throw new \Exception();
		});
		
		$this->call('orchestra::credential@register', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::register'));
		$this->assertTrue(is_string(\Session::get('message')));

		\Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/credential/register
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostRegisterPageIsSuccessful()
	{
		$post = array(
			'email'              => 'foobar@register-test.com',
			'fullname'           => 'Test Register Foobar',
			'password'           => '123456',
			\Session::csrf_token => \Session::token(),
		);

		unset(\IoC::$registry['orchestra.user: register']);
		\Orchestra\Core::memory()->put('site.users.registration', true);
		
		$this->call('orchestra::credential@register', array(), 'POST', $post);

		$user = \Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertRedirectedTo(handles('orchestra::login'));
		$this->assertInstanceOf('\Orchestra\Model\User', \IoC::resolve('orchestra.user: register'));
		$this->assertFalse(is_null($user));
	}

	/**
	 * Test Request POST (orchestra)/credential/register email was not sent
	 * 
	 * @test
	 * @group routing
	 */
	public function testPostRegisterPageEmailWasNotSent()
	{
		$post = array(
			'email'              => 'foobar@register-test.com',
			'fullname'           => 'Test Register Foobar',
			'password'           => '123456',
			\Session::csrf_token => \Session::token(),
		);

		unset(\IoC::$registry['orchestra.user: register']);
		\Orchestra\Core::memory()->put('site.users.registration', true);

		$this->getMailerMock();
		
		$this->call('orchestra::credential@register', array(), 'POST', $post);

		$user = \Orchestra\Model\User::where_email('foobar@register-test.com')->first();

		$this->assertRedirectedTo(handles('orchestra::login'));
		$this->assertInstanceOf('\Orchestra\Model\User', \IoC::resolve('orchestra.user: register'));
		$this->assertFalse(is_null($user));
	}
}