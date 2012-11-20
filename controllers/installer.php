<?php

use Orchestra\Core,
	Orchestra\Installer,
	Orchestra\Installer\Runner,
	Orchestra\Messages,
	Orchestra\View;

class Orchestra_Installer_Controller extends Controller {

	/**
	 * Construct Installer Controller with some pre-define configuration
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		Config::set('orchestra::navigation.show-user-box', false);

		$memory = Core::memory();
		$memory->put('site_name', 'Orchestra Installer');

		View::share('orchestra_memory', $memory);
	}

	/**
	 * Initiate Installer and show database and environment setting
	 *
	 * ANY (:bundle)/installer
	 *
	 * @access public
	 * @return Response
	 */
	public function action_index()
	{
		Session::flush();

		$driver   = Config::get('database.default', 'mysql');
		$database = Config::get('database.connections.'.$driver, array());
		$auth     = Config::get('auth');

		// for security, we shouldn't expose database connection to anyone.
		if (isset($database['password'])
			and ($password = strlen($database['password'])))
		{
			$database['password'] = str_repeat('*', $password);
		}

		// check database connection, we should be able to indicate the user
		// whether the connection is working or not.
		$database['status'] = Installer::check_database();

		$auth_status = true;

		if ($auth['driver'] === 'eloquent')
		{
			if (class_exists($auth['model'])) $driver = new $auth['model'];

			if ( ! (isset($driver) and $driver instanceof Orchestra\Model\User))
			{
				$auth_status = false;
			}
		}

		$data = array(
			'database'      => $database,
			'auth'          => $auth,
			'auth_status'   => $auth_status,
		);

		return View::make('orchestra::installer.index', $data);
	}

	/**
	 * Installation steps, migrate database as well as create first
	 * administration user for current application
	 *
	 * ANY (:bundle)/installer/steps/(:step)
	 *
	 * @access public
	 * @param  integer $step step number
	 * @return Response
	 */
	public function action_steps($step)
	{
		$data = array(
			'site_name' => 'Orchestra',
		);

		switch (intval($step))
		{
			case 1 :
				// step 1 involve running basic database migrations so we
				// can run Orchestra properly. Extension migration will not
				// be done at this point.
				Runner::install();

				return View::make('orchestra::installer.step1', $data);
				break;

			case 2 :
				Session::flush();

				// Step 2 involve creating administation user account for
				// current application.
				if (Runner::create_user(Input::all()))
				{
					return View::make('orchestra::installer.step2', $data);
				}
				else
				{
					$message = new Messages;
					$message->add('error', 'Unable to create user');

					return Redirect::to(handles('orchestra::installer/steps/1'))
							->with('message', serialize($message));
				}
				break;

		}
	}
}
