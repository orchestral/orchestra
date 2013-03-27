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
	 * @group routing
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

		$this->call('orchestra::installer@index', array());
		$this->assertViewIs('orchestra::installer.index');
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
	 * @group routing
	 */
	public function testGetInstallerPageSuccessful()
	{
		$this->call('orchestra::installer@steps', array(100));
		$this->assertResponseNotFound();

		$this->call('orchestra::installer@index', array());
		$this->assertViewIs('orchestra::installer.index');

		$this->call('orchestra::installer@steps', array(1));
		$this->assertViewIs('orchestra::installer.step1');

		$this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin+orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));
		$this->assertRedirectedTo(handles('orchestra::installer/steps/1'));

		$this->call('orchestra::installer@steps', array(2), 'POST', array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin@orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));
		$this->assertViewIs('orchestra::installer.step2');
	}
}

class InstallerAuthStub extends \Orchestra\Model\User {}