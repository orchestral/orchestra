<?php

Bundle::start('orchestra');

class RoutingUsersTest extends Orchestra\Testable\TestCase {

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
	 * Test Request GET (orchestra)/users without auth
	 *
	 * @test
	 */
	public function testGetUsersPageWithoutAuth()
	{
		$response = $this->call('orchestra::users@index');
		
		$this->assertInstanceOf('Laravel\Redirect', $response);	
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/users
	 *
	 * @test
	 */
	public function testGetUsersPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::users@index');
		
		$this->assertInstanceOf('Laravel\Response', $response);	
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::users.index', $response->content->view);
	}

	/**
	 * Test Request GET (orchestra)/users/view/1 without auth
	 *
	 * @test
	 */
	public function testGetSingleUserPageWithoutAuth()
	{
		$response = $this->call('orchestra::users@view', array(1));
		
		$this->assertInstanceOf('Laravel\Redirect', $response);	
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/users/view/1
	 *
	 * @test
	 */
	public function testGetSingleUserPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::users@view', array(1));
		
		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::users.edit', $response->content->view);

		$response = $this->call('orchestra::users@view', array(''));
		
		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::users.edit', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/users/view fails validation
	 *
	 * @test
	 */
	public function testPostCreateNewUserPageFailsValidation()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::users@view', array(''), 'POST', array(
			'id'       => '',
			'email'    => 'crynobone+gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::users/view'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request POST (orchestra)/users/view
	 *
	 * @test
	 */
	public function testPostCreateNewUserPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::users@view', array(''), 'POST', array(
			'id'       => '',
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
			'roles'    => array(2),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::users'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::where_email('crynobone@gmail.com')->first();

		$this->assertGreaterThan(0, $user->id);
		$this->assertEquals('crynobone@gmail.com', $user->email);
		$this->assertEquals('Mior Muhammad Zaki', $user->fullname);
		$this->assertTrue(Hash::check('123456', $user->password));

		$user->delete();
	}

	/**
	 * Test Request POST (orchestra)/users/view/1
	 *
	 * @test
	 */
	public function testPostUpdateUserPage()
	{
		$this->be($this->user);

		$user = Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2));

		$response = $this->call('orchestra::users@view', array($user->id), 'POST', array(
			'id'       => $user->id,
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'crynobone',
			'password' => '345678',
			'roles'    => array(2),
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::users'), 
			$response->foundation->headers->get('location'));

		$updated_user = Orchestra\Model\User::find($user->id);

		$this->assertEquals('crynobone@gmail.com', $updated_user->email);
		$this->assertEquals('crynobone', $updated_user->fullname);
		$this->assertTrue(Hash::check('345678', $updated_user->password));

		$updated_user->delete();
	}
	
	/**
	 * Test Request POST (orchestra)/users/view/1
	 *
	 * @test
	 */
	public function testPostDeleteUserPage()
	{
		$this->be($this->user);

		$user = Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2));

		$this->assertGreaterThan(0, $user->id);

		$user_id = $user->id;
		
		$response = $this->call('orchestra::users@delete', array($user_id));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::users'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::find($user_id);

		$this->assertTrue(is_null($user));
	}

	/**
	 * Test Request POST (orchestra)/users/view/1 with multiple roles
	 *
	 * @test
	 */
	public function testPostDeleteUserPageWithMultipleRoles()
	{
		$this->be($this->user);

		$user = Orchestra\Model\User::create(array(
			'email'    => 'crynobone@gmail.com',
			'fullname' => 'Mior Muhammad Zaki',
			'password' => '123456',
		));
		$user->roles()->sync(array(2, 1));

		$this->assertGreaterThan(0, $user->id);

		$user_id = $user->id;
		
		$response = $this->call('orchestra::users@delete', array($user_id));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::users'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::find($user_id);

		$this->assertTrue(is_null($user));
	}
}