<?php namespace Orchestra\Testable;

use \Bundle,
	\Config,
	\DB,
	\File,
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

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'Orchestra\Model\User');
		Config::set('database.default', 'sqlite');
		Config::set('database.connections.sqlite', array(
			'driver'   => 'sqlite',
			'database' => 'orchestra',
			'prefix'   => '',
		));

		DB::$connections = array();
		
		// load the session.
		Session::load();

		$this->client = $this->createClient();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		File::delete(path('storage').'database'.DS.'orchestra.sqlite');
		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
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
		if ( ! \Orchestra\Installer::installed())
		{
			Request::$foundation = LaravelRequest::createFromGlobals();

			Request::foundation()->server->add(array(
				'REQUEST_METHOD' => 'POST',
			));

			\Orchestra\Installer\Runner::install();

			\Orchestra\Installer\Runner::create_user(array(
				'site_name' => 'Orchestra',
				'email'     => 'example@test.com',
				'password'  => '123456',
				'fullname'  => 'Orchestra TestRunner',
			));

			\Orchestra\Core::shutdown();
			\Orchestra\Memory::shutdown();
			\Orchestra\Acl::shutdown();

			\Orchestra\Core::start();
		}
	}
}