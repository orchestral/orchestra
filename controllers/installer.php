<?php

use \Request,
	Orchestra\Core,
	Orchestra\Installer,
	Orchestra\Installer\Publisher,
	Orchestra\Installer\Requirement,
	Orchestra\Installer\Runner,
	Orchestra\Messages,
	Orchestra\Site,
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

		Site::set('navigation::show-user-box', false);
		Core::memory()->put('site.name', 'Orchestra Installer');
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
		$requirement = new Requirement(new Publisher);

		Session::flush();

		$driver      = Config::get('database.default', 'mysql');
		$database    = Config::get("database.connections.{$driver}", array());
		$auth        = Config::get('auth');
		$installable = $requirement->installable();

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
			'requirements' => $requirement->checklist(),
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
				if ('POST' !== Request::method() or Runner::create_user(Input::all()))
				{
					return View::make('orchestra::installer.step2', $data);
				}
				else
				{
					$msg = Messages::make();
					$msg->add('error', 'Unable to create user');
					$msg->save();

					return Redirect::to(handles('orchestra::installer/steps/1'));
				}
				break;
		}

		return Response::error('404');
	}
}
