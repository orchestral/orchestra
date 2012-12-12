<?php namespace Orchestra\Testable;

use \Auth, 
	\Bundle,
	\Config,
	\Cookie,
	\DB,
	\Event,
	\File,
	\Orchestra as O,
	\PHPUnit_Framework_TestCase,
	\Request,
	\Session,
	\URL,
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

		Event::listen('orchestra.testable: setup-db', function ()
		{
			Config::set('database.connections.testdb', array(
				'driver'   => 'sqlite',
				'database' => 'orchestra',
				'prefix'   => '',
			));
		});

		Event::listen('orchestra.testable: teardown-db', function ()
		{
			File::delete(path('storage').'database'.DS.'orchestra.sqlite');
		});

		$this->client = $this->createClient();

		// Since Orchestra is started by default when we run 
		// Bundle::start('orchestra'), we need to restart everything before 
		// running any testcases.
		$this->restartApplication();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->removeApplication();
		unset($this->client);

		O\Core::shutdown();
		Event::first('orchestra.testable: teardown-db');
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
		Config::set('database.default', 'testdb');
		Event::first('orchestra.testable: setup-db');

		Config::set('application.url', 'http://localhost');
		Config::set('application.index', '');
		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'Orchestra\Model\User');

		if ( ! O\Installer::installed())
		{
			Request::$foundation = LaravelRequest::createFromGlobals();
			Session::load();

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
		}

		// Request and Session instance need to be flushed an restarted.
		Request::$foundation = LaravelRequest::createFromGlobals();
		Session::$instance   = null;

		Session::load();
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
		Auth::$drivers       = null;
		DB::$connections     = array();
		Cookie::$jar         = array();
		Session::$instance   = null;
		URL::$base           = null;
		O\Installer::$status = false;

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
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