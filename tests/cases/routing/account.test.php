<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class AccountTest extends \Orchestra\Testable\TestCase {

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
	 * Test Request GET (orchestra)/account when not logged-in
	 *
	 * @test
	 * @group routing
	 */
	public function testGetEditProfilePageWithoutAuth()
	{
		$this->call('orchestra::account@index', array());
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/account
	 *
	 * @test
	 * @group routing
	 */
	public function testGetEditProfilePage()
	{
		$this->be($this->user);
		$this->call('orchestra::account@index', array());
		$this->assertViewIs('orchestra::account.index');
	}

	/**
	 * Test Request GET (orchestra)/account/password when not logged-in
	 *
	 * @test
	 * @group routing
	 */
	public function testGetEditPasswordPageWithoutAuth()
	{
		$this->call('orchestra::account@password', array());
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/account/password
	 *
	 * @test
	 * @group routing
	 */
	public function testGetEditPasswordPage()
	{
		$this->be($this->user);
		$this->call('orchestra::account@password', array());
		$this->assertViewIs('orchestra::account.password');
	}

	/**
	 * Test Request POST (orchestra)/account
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditProfilePage()
	{
		$post = array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => $this->user->email,
		);

		$this->be($this->user);
		$this->call('orchestra::account@index', array(), 'POST', $post);

		$user = \Orchestra\Model\User::find(1);

		$this->assertRedirectedTo(handles('orchestra::account'));
		$this->assertEquals('Foobar', $user->fullname);
		$this->assertEmpty(\Session::get('errors'));
	}

	/**
	 * Test Request POST (orchestra)/account with validation error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditProfilePageWithValidationError()
	{
		$post = array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => 'foo+bar.com',
		);

		$this->be($this->user);
		$this->call('orchestra::account@index', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::account'));
		$this->assertTrue(array() !== \Session::get('errors', array()));
	}

	/**
	 * Test Request POST (orchestra)/account with database error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditProfilePageDatabaseError()
	{
		$post = array(
			'id'       => $this->user->id,
			'fullname' => 'Foobar',
			'email'    => $this->user->email,
		);

		$events = \Event::$events;
		\Event::listen('eloquent.saving: Orchestra\Model\User', function ($model)
		{
			throw new \Exception();
		});

		$this->be($this->user);
		$this->call('orchestra::account@index', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::account'));
		$this->assertTrue(is_string(\Session::get('message')));

		\Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/account/password
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditPasswordPage()
	{
		$post = array(
			'id'               => $this->user->id,
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '123',
		);

		$this->be($this->user);
		$this->call('orchestra::account@password', array(), 'POST', $post);

		$user = \Orchestra\Model\User::find(1);

		$this->assertRedirectedTo(handles('orchestra::account/password'));
		$this->assertEmpty(\Session::get('errors'));
		$this->assertTrue(\Hash::check('123', $user->password));

		// Revert the changes.
		$user->password = '123456';
		$user->save();
	}

	/**
	 * Test Request POST (orchestra)/account/password with database error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditPasswordPageDatabaseError()
	{
		$post = array(
			'id'               => $this->user->id,
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '123',
		);

		$events = \Event::$events;
		\Event::listen('eloquent.saving: Orchestra\Model\User', function($model)
		{
			throw new \Exception();
		});

		$this->be($this->user);
		$this->call('orchestra::account@password', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::account/password'));
		$this->assertTrue(is_string(\Session::get('message')));

		\Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/account/password with validation error 
	 * when new password and confirm password is not the same.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditPasswordPageMismatchValidationError()
	{
		$post = array(
			'id'               => $this->user->id,
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '1233',
		);

		$this->be($this->user);
		$this->call('orchestra::account@password', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::account/password'));
		$this->assertTrue(array() !== \Session::get('errors', array()));
	}

	/**
	 * Test Request POST (orchestra)/account/password with validation error 
	 * when old password is not correct.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostEditPasswordPageIncorrectOldPasswordError()
	{
		$post = array(
			'id'               => $this->user->id,
			'current_password' => '123467',
			'new_password'     => '123',
			'confirm_password' => '123',
		);

		$this->be($this->user);
		$this->call('orchestra::account@password', array(), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::account/password'));
		$this->assertTrue(array() !== \Session::get('message', array()));
	}
}