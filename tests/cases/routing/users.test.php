<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class UsersTest extends \Orchestra\Testable\TestCase {

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
	 * Test Request GET (orchestra)/users without auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetUsersPageWithoutAuth()
	{
		$this->call('orchestra::users@index');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/users
	 *
	 * @test
	 * @group routing
	 */
	public function testGetUsersPage()
	{
		$this->be($this->user);
		$this->call('orchestra::users@index');
		$this->assertViewIs('orchestra::users.index');
	}

	/**
	 * Test Request GET (orchestra)/users/view/1 without auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetSingleUserPageWithoutAuth()
	{
		$this->call('orchestra::users@view', array(1));
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/users/view/1
	 *
	 * @test
	 * @group routing
	 */
	public function testGetSingleUserPage()
	{
		$this->be($this->user);

		$this->call('orchestra::users@view', array(1));
		$this->assertViewIs('orchestra::users.edit');

		$this->call('orchestra::users@view', array(''));
		$this->assertViewIs('orchestra::users.edit');
	}

	/**
	 * Test Request POST (orchestra)/users/view with validation error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostUserPageValidationError()
	{
		$post = array(
			'id'       => '',
			'email'    => 'crynobone+gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		);

		$this->be($this->user);
		$this->call('orchestra::users@view', array(''), 'POST', $post);
		$this->assertRedirectedTo(handles('orchestra::users/view/'));
	}

	/**
	 * Test Request POST (orchestra)/users/view
	 *
	 * @test
	 * @group routing
	 */
	public function testPostCreateNewUserPage()
	{
		$post = array(
			'id'       => '',
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		);

		$this->be($this->user);
		$this->call('orchestra::users@view', array(''), 'POST', $post);

		$user = \Orchestra\Model\User::where_email('crynobone@gmail.com')->first();

		$this->assertRedirectedTo(handles('orchestra::users'));
		$this->assertGreaterThan(0, $user->id);
		$this->assertEquals('crynobone@gmail.com', $user->email);
		$this->assertEquals('Mior Muhammad Zaki', $user->fullname);
		$this->assertTrue(\Hash::check('123456', $user->password));

		$user->delete();
	}

	/**
	 * Test Request POST (orchestra)/users/view with invalid user id.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostCreateNewUserPageInvalidUserId()
	{
		$post = array(
			'id'       => '1',
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		);

		$this->be($this->user);
		$this->call('orchestra::users@view', array(''), 'POST', $post);
		$this->assertResponseIs(500);
	}

	/**
	 * Test Request POST (orchestra)/users/view on create or edit have 
	 * database error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostUserPageDatabaseError()
	{
		$events = \Event::$events;
		$post   = array(
			'id'       => '',
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		);

		\Event::listen('eloquent.saving: Orchestra\Model\User', function ($model)
		{
			throw new \Exception();
		});

		$this->be($this->user);
		$this->call('orchestra::users@view', array(''), 'POST', $post);

		$user = \Orchestra\Model\User::where_email('crynobone@gmail.com')->first();

		$this->assertRedirectedTo(handles('orchestra::users'));
		$this->assertNull($user);

		\Event::$events = $events;
	}

	/**
	 * Test Request POST (orchestra)/users/view/(:num) on update.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostUpdateUserPage()
	{
		$user = \Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2));
		
		$post = array(
			'id'       => $user->id,
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'crynobone',
			'password' => '345678',
			'roles'    => array(2),
		);

		$this->be($this->user);
		$this->call('orchestra::users@view', array($user->id), 'POST', $post);

		$updated_user = \Orchestra\Model\User::find($user->id);

		$this->assertRedirectedTo(handles('orchestra::users'));
		$this->assertEquals('crynobone@gmail.com', $updated_user->email);
		$this->assertEquals('crynobone', $updated_user->fullname);
		$this->assertTrue(\Hash::check('345678', $updated_user->password));

		$updated_user->delete();
	}
	
	/**
	 * Test Request POST (orchestra)/users/view/1
	 *
	 * @test
	 * @group routing
	 */
	public function testPostDeleteUserPage()
	{
		$user = \Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2));

		$this->assertGreaterThan(0, $user->id);

		$user_id = $user->id;
		
		$this->be($this->user);
		$this->call('orchestra::users@delete', array($user_id));

		$user = \Orchestra\Model\User::find($user_id);

		$this->assertRedirectedTo(handles('orchestra::users'));
		$this->assertNull($user);
	}

	/**
	 * Test Request POST (orchestra)/users/view/1 with multiple roles
	 *
	 * @test
	 * @group routing
	 */
	public function testPostDeleteUserPageWithMultipleRoles()
	{
		$user = \Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2, 1));

		$this->assertGreaterThan(0, $user->id);

		$user_id = $user->id;
		
		$this->be($this->user);
		$this->call('orchestra::users@delete', array($user_id));

		$user = \Orchestra\Model\User::find($user_id);

		$this->assertRedirectedTo(handles('orchestra::users'));
		$this->assertNull($user);
	}

	/**
	 * Test Request POST (orchestra)/users/view/1 with id or user data error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostDeleteUserPageIdError()
	{
		$this->be($this->user);
		$this->call('orchestra::users@delete', array(20000));
		$this->assertResponseNotFound();
	}

	/**
	 * Test Request POST (orchestra)/users/view/1 with database error.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostDeleteUserDatabaseError()
	{
		$user = \Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2, 1));

		$this->assertGreaterThan(0, $user->id);

		$events = \Event::$events;
		\Event::listen('eloquent.deleting: Orchestra\Model\User', function ($model)
		{
			throw new \Exception();
		});

		$user_id = $user->id;
		
		$this->be($this->user);
		$this->call('orchestra::users@delete', array($user_id));
		$this->assertRedirectedTo(handles('orchestra::users'));

		\Event::$events = $events;
	}
}