<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class InstallerTest extends \Orchestra\Testable\TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->removeApplication();

		\Session::load();

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
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
	 * Test Request GET (orchestra)/installer/index failed
	 *
	 * @test
	 */
	public function testGetInstallerPageFailed()
	{
		$driver    = \Config::get('database.default', 'mysql');
		$database  = \Config::get("database.connections.{$driver}", array());
		$auth      = \Config::get('auth');
		$dummyauth = array_merge($auth, array(
			'model' => 'Orchestra\Tests\Routing\InstallerAuthStub',
		));

		\Auth::$drivers   = null;
		\DB::$connections = array();

		\Config::set('database.default', 'dummy-mysql');
		\Config::set("database.connections.dummy-mysql", array(
			'driver'   => 'mysql',
			'host'     => '127.0.0.1',
			'database' => \Str::random(10),
			'username' => \Str::random(10),
			'password' => \Str::random(10),
			'charset'  => 'utf8',
			'prefix'   => '',
		));
		\Config::set('auth', $dummyauth);

		$response = $this->call('orchestra::installer@index', array());

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.index', $response->content->view);

		$this->assertFalse(\Orchestra\Installer::check_database());

		\Auth::$drivers   = null;
		\DB::$connections = array();

		\Config::set('database.default', $driver);
		\Config::set("database.connections.{$driver}", $database);
		\Config::set('auth', $auth);
	}

	/**
	 * Test Request GET (orchestra)/installer/index successful
	 *
	 * @test
	 */
	public function testGetInstallerPageSuccessful()
	{
		$response = $this->call('orchestra::installer@steps', array(100));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());

		$response = $this->call('orchestra::installer@index', array());

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.index', $response->content->view);

		$response = $this->call('orchestra::installer@steps', array(1));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.step1', $response->content->view);

		$response = $this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin+orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::installer/steps/1'), 
			$response->foundation->headers->get('location'));

		$response = $this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin@orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::installer.step2', $response->content->view);
	}
}

class InstallerAuthStub extends \Orchestra\Model\User {}