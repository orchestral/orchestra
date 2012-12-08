<?php


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

		$this->be($this->user);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->user->fill(array(
			'fullname' => 'Orchestra TestRunner',
			'password' => '123456',
		));
		$this->user->save();

		unset($this->user);
		$this->be(null);
		parent::tearDown();
	}

	/**
	 * Test Request GET (orchestra)/account
	 *
	 * @test
	 */
	public function testGetEditProfilePage()
	{
		$response = $this->call('orchestra::account@index', array());

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::account.index', $response->content->view);
	}

	/**
	 * Test Request GET (orchestra)/account/password
	 *
	 * @test
	 */
	public function testGetEditPasswordPage()
	{
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
		$response = $this->call('orchestra::account@index', array(), 'POST', array(
			'fullname' => 'Foobar',
			'email'    => $this->user->email,
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(handles('orchestra::account'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::find(1);

		$this->assertEquals('Foobar', $user->fullname);
	}

	/**
	 * Test Request POST (orchestra)/account
	 *
	 * @test
	 */
	public function testPostEditPasswordPage()
	{
		$response = $this->call('orchestra::account@password', array(), 'POST', array(
			'current_password' => '123456',
			'new_password'     => '123',
			'confirm_password' => '123',
		));

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(handles('orchestra::account/password'), 
			$response->foundation->headers->get('location'));

		$user = Orchestra\Model\User::find(1);

		$this->assertTrue(Hash::check('123', $user->password));

	}
}