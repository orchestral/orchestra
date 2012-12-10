<?php namespace Orchestra\Testable;

use \Auth, 
	\Bundle,
	\Config,
	\Cookie,
	\DB,
	\Orchestra as O,
	\PHPUnit_Framework_TestCase,
	\Request,
	\Session,
	Symfony\Component\HttpFoundation\LaravelRequest;

abstract class TestCase extends PHPUnit_Framework_TestCase {

	/**
	 * The client instance.
	 *
	 * @var Orchestra\Testable\CLient
	 */
	protected $client;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('storage', $base_path.'storage'.DS);

		Config::set('database.default', 'sqlite');
		Config::set('database.connections.sqlite', array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		));
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', '');

		$this->client = $this->createClient();
		$this->createApplication();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->removeApplication();
		unset($this->client);

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');

		O\Core::shutdown();
	}

	/**
	 * Call a controller.
	 *
	 * @access public
	 * @return mixed
	 */
	public function call()
	{
		return call_user_func_array(array($this->client, 'call'), func_get_args());
	}

	/**
	 * Create a new client.
	 *
	 * @access public
	 * @return Orchestra\Testable\Client
	 */
	public function createClient()
	{
		return new Client;
	}

	/**
	 * Mock login as a user
	 *
	 * @access public			
	 * @param  mixed    $user   Login as a user when $user is instance of 
	 *                          Orchestra\Model\User
	 * @param  mixed    $driver
	 * @return void
	 */
	public function be(\Orchestra\Model\User $user = null, $driver = null)
	{
		if (is_null($user))
		{
			Auth::driver($driver)->logout();
			return ;
		}

		Auth::driver($driver)->login($user->id);
	}

	/**
	 * Create application
	 *
	 * @access public
	 * @return void
	 */
	public function createApplication()
	{
		Auth::$drivers     = null;
		Cookie::$jar       = array();
		Session::$instance = null;

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'Orchestra\Model\User');

		DB::$connections = array();
		
		// load the session.
		Session::load();

		if ( ! O\Installer::installed())
		{
			Request::$foundation = LaravelRequest::createFromGlobals();

			Request::foundation()->server->add(array(
				'REQUEST_METHOD' => 'POST',
			));

			O\Installer\Runner::install();

			O\Installer\Runner::create_user(array(
				'site_name' => 'Orchestra',
				'email'     => 'example@test.com',
				'password'  => '123456',
				'fullname'  => 'Orchestra TestRunner',
			));

			O\Core::shutdown();
			O\Memory::shutdown();
			O\Acl::shutdown();
		}

		O\Core::start();
	}

	/**
	 * Remove Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function removeApplication()
	{
		Auth::$drivers = null;
		Cookie::$jar = array();
		Session::$instance = null;
		
		O\Installer::$status = false;
		O\Extension::shutdown();
	}

	/**
	 * Restart Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function restartApplication()
	{
		$this->removeApplication();
		$this->createApplication();
	}
}