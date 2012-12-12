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
	 * The application instance.
	 *
	 * @var Orchestra\Testable\Application
	 */
	protected $app;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('storage', $base_path.'storage'.DS);

		if ( ! Event::listeners('orchestra.testable: setup-db'))
		{
			Event::listen('orchestra.testable: setup-db', function ()
			{
				Config::set('database.connections.testdb', array(
					'driver'   => 'sqlite',
					'database' => 'orchestra',
					'prefix'   => '',
				));
			});
		}

		if ( ! Event::listeners('orchestra.testable: teardown-db'))
		{
			Event::listen('orchestra.testable: teardown-db', function ()
			{
				File::delete(path('storage').'database'.DS.'orchestra.sqlite');
			});
		}

		$this->createClient();
		$this->createApplication();
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
		$this->client = new Client;
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
		$this->app = new Application;
	}

	/**
	 * Remove Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function removeApplication()
	{
		if ($this->app instanceof Application) $this->app->remove();
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