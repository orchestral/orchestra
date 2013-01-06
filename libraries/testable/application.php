<?php namespace Orchestra\Testable;

use \Auth,
	\Config,
	\Cookie,
	\DB,
	\Event,
	\IoC,
	\Orchestra as O,
	\Request,
	\Session,
	\URL,
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
		O\Installer::$status = false;

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
		O\Installer::$status = false;

		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'User');
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}
}