<?php namespace Orchestra\Testable;

use Auth,
	Config,
	Cookie,
	DB,
	Event,
	IoC,
	Orchestra\Core,
	Orchestra\Installer,
	Orchestra\Model\User as User,
	Request,
	Session,
	URL,
	Symfony\Component\HttpFoundation\LaravelRequest;

class Application {
	
	/**
	 * Construct a new application
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		IoC::register('orchestra.mailer', function ($from = true)
		{
			return Mailer::instance();
		});

		Config::set('database.default', 'testdb');
		Event::first('orchestra.testable: setup-db');

		Installer::$status = false;
		URL::$base = null;

		Config::set('application.url', 'http://localhost');
		Config::set('application.index', '');
		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'Orchestra\Model\User');
		
		if ( ! Installer::installed()) 
		{
			$this->installation();
			Core::shutdown();
		}

		// Request and Session instance need to be flushed an restarted.
		Request::$foundation = LaravelRequest::createFromGlobals();
		Session::$instance   = null;

		Session::load();
		Core::start();
	}

	/**
	 * Application installation.
	 *
	 * @access protected
	 * @return void
	 */
	protected function installation()
	{
		Request::$foundation = LaravelRequest::createFromGlobals();
		Session::load();

		Request::foundation()->server->add(array(
			'REQUEST_METHOD' => 'POST',
		));

		Installer\Runner::install();

		Installer\Runner::create_user(array(
			'site_name' => 'Orchestra Test Suite',
			'email'     => 'admin@orchestra.com',
			'password'  => '123456',
			'fullname'  => 'Test Administrator',
		));

		$foouser = User::where_email('member@orchestra.com')->first();

		if (is_null($foouser))
		{
			$foouser = User::create(array(
				'fullname' => 'Test User',
				'email'    => 'member@orchestra.com',
				'password' => '123456',
				'status'   => User::VERIFIED,
			));
			$foouser->roles()->sync(array(
				Config::get('orchestra::orchestra.member_role'),
			));
		}
	}

	/**
	 * Shutdown application.
	 *
	 * @access public
	 * @return void
	 */
	public function shutdown()
	{
		Core::shutdown();
		$this->remove();
	}

	/**
	 * Remove application.
	 *
	 * @access public
	 * @return void
	 */
	public function remove()
	{
		Auth::$drivers       = null;
		DB::$connections     = array();
		Cookie::$jar         = array();
		Session::$instance   = null;
		URL::$base           = null;
		Installer::$status = false;

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}
}