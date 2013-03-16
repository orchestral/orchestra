<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class ForgotTest extends \Orchestra\Testable\TestCase {

	/**
	 * User instance
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
	 * Get Mailer Mock
	 *
	 * @test
	 * @group routing
	 */
	public function getMailerMock()
	{
		$mock = $mock = $this->getMockBuilder('\Orchestra\Testable\Mailer')
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
	 * Test Request GET (orchestra)/forgot
	 *
	 * @test
	 * @group routing
	 */
	public function testGetForgotPage()
	{
		$response = $this->call('orchestra::forgot@index');

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::forgot.index', $response->content->view);
	}

	/**
	 * Test Request GET (orchestra)/forgot with auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetForgotPageWithAuth()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::forgot@index');

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request POST (orchestra)/forgot with invalid csrf
	 *
	 * @test
	 * @group routing
	 */
	public function testPostForgotPageFailedInvalidCsrf()
	{
		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email' => 'admin@orchestra.com',
		));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());
	}

	/**
	 * Test Request POST (orchestra)/forgot
	 *
	 * @test
	 * @group routing
	 */
	public function testPostForgotPage()
	{
		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email'              => 'admin@orchestra.com',
			\Session::csrf_token => \Session::token(),
		));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::forgot'), 
			$response->foundation->headers->get('location'));

		// Mimic shutting down Orchestra.
		\Orchestra\Core::shutdown();

		$meta = \Orchestra\Model\User\Meta::where('user_id', '=', 1)
					->where('name', '=', 'reset_password_hash')
					->first();

		$this->assertNotNull($meta);
		$this->assertNotNull($meta->value);
	}

	/**
	 * Test Request POST (orchestra)/forgot with validation error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostForgotPageValidationError()
	{
		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email'              => 'admin+orchestra.com',
			\Session::csrf_token => \Session::token(),
		));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::forgot'), 
			$response->foundation->headers->get('location'));
		$this->assertInstanceOf('\Laravel\Messages', \Session::get('errors'));
	}

	/**
	 * Test Request POST (orchestra)/forgot with user not found error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostForgotPageUserNotFoundError()
	{
		$hash     = \Str::random(10);
		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email'              => "{$hash}@{$hash}.com",
			\Session::csrf_token => \Session::token(),
		));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::forgot'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(is_string(\Session::get('message')));
	}

	/**
	 * Test Request POST (orchestra)/forgot with email wasn't sent error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostForgotPageEmailWasNotSentError()
	{
		$this->getMailerMock();

		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email'              => "admin@orchestra.com",
			\Session::csrf_token => \Session::token(),
		));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::forgot'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(is_string(\Session::get('message')));
	}

	/**
	 * Test Request GET (orchestra)/forgot/reset/(id)/(hash)
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResetPasswordPage()
	{
		$meta = \Orchestra\Memory::make('user');
		$hash = \Str::random(32);
		$meta->put('reset_password_hash.1', $hash);

		$response = $this->call('orchestra::forgot@reset', array(1, $hash));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));

		$user = \Orchestra\Model\User::find(1);

		$this->assertFalse(\Hash::check('123456', $user->password));
	}

	/**
	 * Test Request GET (orchestra)/forgot/reset/(id)/(hash) with invalid 
	 * argument error.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResetPasswordPageInvalidArgumentError()
	{
		$response = $this->call('orchestra::forgot@reset', array('hello world', ''));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
	}

	/**
	 * Test Request GET (orchestra)/forgot/reset/(id)/(hash) with invalid 
	 * hash error.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResetPasswordPageInvalidHashError()
	{
		$meta = \Orchestra\Memory::make('user');
		$hash = \Str::random(32);
		$meta->put('reset_password_hash.1', $hash);

		$response = $this->call('orchestra::forgot@reset', array(1, \Str::random(30)));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
	}

	/**
	 * Test Request GET (orchestra)/forgot/reset/(id)/(hash) email wasn't 
	 * sent error.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetResetPasswordPageEmailWasNotSentError()
	{
		$meta = \Orchestra\Memory::make('user');
		$hash = \Str::random(32);
		$meta->put('reset_password_hash.1', $hash);

		$this->getMailerMock();

		$response = $this->call('orchestra::forgot@reset', array(1, $hash));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(is_string(\Session::get('message')));
	}
}