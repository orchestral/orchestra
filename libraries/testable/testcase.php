<?php namespace Orchestra\Testable;

use \Auth, 
	\Bundle,
	\Config,
	\Cookie,
	\DB,
	\File,
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
			'database' => 'orchestra',
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

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}

	/**
	 * Call a controller.
	 */
	public function call()
	{
		return call_user_func_array(array($this->client, 'call'), func_get_args());
	}

	/**
	 * Create a new client.
	 */
	public function createClient()
	{
		return new Client;
	}

	/**
	 * Create application
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
	 * Remove Application
	 */
	public function removeApplication()
	{
		Auth::$drivers = null;
		Cookie::$jar = array();
		Session::$instance = null;
		
		O\Installer::$status = false;
		File::delete(path('storage').'database'.DS.'orchestra.sqlite');
	}

	/**
	 * Restart Application
	 */
	public function restartApplication()
	{
		$this->removeApplication();
		$this->createApplication();
	}
}