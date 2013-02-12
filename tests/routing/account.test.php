<?php

Bundle::start('orchestra');

class RoutingAccountTest extends Orchestra\Testable\TestCase {

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
	 * Test Request GET (orchestra)/account when not logged-in
	 *
	 * @test
	 */
	public function testGetEditProfilePageWithoutAuth()
	{
		$response = $this->call('orchestra::account@index', array());

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/account
	 *
	 * @test
	 */
	public function testGetEditProfilePage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@index', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::account.index', $response->content->view);
	}

	/**
	 * Test Request GET (orchestra)/account/password when not logged-in
	 *
	 * @test
	 */
	public function testGetEditPasswordPageWithoutAuth()
	{
		$response = $this->call('orchestra::account@password', array());

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/account/password
	 *
	 * @test
	 */
	public function testGetEditPasswordPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@password', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::account.password', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/account
	 *
	 * @test
	 */
	public function testPostEditProfilePage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@index', array(), 'POST', array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => $this->user->email,
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::find(1);

		$this->assertEquals('Foobar', $user->fullname);
		$this->assertEmpty(Session::get('errors'));
	}

	/**
	 * Test Request POST (orchestra)/account with validation error.
	 *
	 * @test
	 */
	public function testPostEditProfilePageWithValidationError()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@index', array(), 'POST', array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => 'foo+bar.com',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(array() !== Session::get('errors', array()));
	}

	/**
	 * Test Request POST (orchestra)/account with database error.
	 *
	 * @test
	 */
	public function testPostEditProfilePageDatabaseError()
	{
		$this->be($this->user);

		$events = Event::$events;

		Event::listen('eloquent.saving: Orchestra\Model\User', function ($model)
		{
			throw new Exception();
		});

		$response = $this->call('orchestra::account@index', array(), 'POST', array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => $this->user->email,
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(is_string(Session::get('message')));

		Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/account/password
	 *
	 * @test
	 */
	public function testPostEditPasswordPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@password', array(), 'POST', array(
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '123',
		));

		$user = Orchestra\Model\User::find(1);

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account/password'), 
			$response->foundation->headers->get('location'));
		$this->assertEmpty(Session::get('errors'));
		$this->assertTrue(Hash::check('123', $user->password));

		// Revert the changes.
		$user->password = '123456';
		$user->save();
	}

	/**
	 * Test Request POST (orchestra)/account/password with database error.
	 *
	 * @test
	 */
	public function testPostEditPasswordPageDatabaseError()
	{
		$this->be($this->user);

		$events = Event::$events;

		Event::listen('eloquent.saving: Orchestra\Model\User', function($model)
		{
			throw new Exception();
		});

		$response = $this->call('orchestra::account@password', array(), 'POST', array(
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '123',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account/password'),
			$response->foundation->headers->get('location'));
		$this->assertTrue(is_string(Session::get('message')));

		Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/account/password with validation error 
	 * when new password and confirm password is not the same.
	 *
	 * @test
	 */
	public function testPostEditPasswordPageMismatchValidationError()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@password', array(), 'POST', array(
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '1233',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account/password'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(array() !== Session::get('errors', array()));
	}

	/**
	 * Test Request POST (orchestra)/account/password with validation error 
	 * when old password is not correct.
	 *
	 * @test
	 */
	public function testPostEditPasswordPageIncorrectOldPasswordError()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::account@password', array(), 'POST', array(
			'current_password' => '123467',
			'new_password'     => '123',
			'confirm_password' => '123',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::account/password'), 
			$response->foundation->headers->get('location'));
		$this->assertTrue(array() !== Session::get('message', array()));
	}
}