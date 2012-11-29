<?php

use Orchestra\Core,
	Orchestra\HTML,
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
		$publisher    = new Orchestra\Installer\Publisher;
		$installable  = true;
		$requirements = array(
			'storage_writable' => array(
				'is'       => (is_writable(path('storage'))),
				'should'   => true,
				'explicit' => false,
				'data'     => array(
					'path' => HTML::create('code', 'storage', array('title' => path('storage'))),
				),
			),
			'bundle_writable' => array(
				'is'       => (is_writable(path('bundle'))),
				'should'   => true,
				'explicit' => false,
				'data'     => array(
					'path' => HTML::create('code', 'bundle', array('title' => path('bundle'))),
				),
			),
			'asset_writable'  => array(
				'is'       => ($publisher->publish()),
				'should'   => true,
				'explicit' => true,
				'data'     => array(
					'path' => HTML::create('code', 'public'.DS.'bundles', array('title' => path('public').'bundles'.DS)),
				),
			),
		);

		foreach ($requirements as $requirement)
		{
			if ($requirement['is'] !== $requirement['should'] 
				and true === $requirement['explicit'])
			{
				$installable = false;
			}
		}

		Session::flush();

		$driver   = Config::get('database.default', 'mysql');
		$database = Config::get("database.connections.{$driver}", array());
		$auth     = Config::get('auth');

		// for security, we shouldn't expose database connection to anyone.
		if (isset($database['password'])
			and ($password = strlen($database['password'])))
		{
			$database['password'] = str_repeat('*', $password);
		}

		// check database connection, we should be able to indicate the user
		// whether the connection is working or not.
		if ( ! ($database['status'] = Installer::check_database()))
		{
			$installable = false;
		}

		$auth_status = false;

		if ($auth['driver'] === 'eloquent')
		{
			if (class_exists($auth['model'])) $driver = new $auth['model'];

			if (isset($driver) and $driver instanceof Orchestra\Model\User)
			{
				$auth_status = true;
			}
		}

		(true === $auth_status) or $installable = false;

		$data = array(
			'database'     => $database,
			'auth'         => $auth,
			'auth_status'  => $auth_status,
			'installable'  => $installable,
			'requirements' => $requirements,
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
