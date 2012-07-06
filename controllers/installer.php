<?php

use \Exception, Orchestra\Installer\Runner;

class Orchestra_Installer_Controller extends Controller 
{

	protected $memory = null;

	public function __construct()
	{
		parent::__construct();

		$this->memory = Orchestra\Core::memory();
		$this->memory->put('site_name', 'Orchestra Installer');
		Config::set('orchestra::navigation.show-user-box', false);
	}

	public function action_index() 
	{
		Session::flush();

		$database = Config::get('database.connections.'.Config::get('database.default', 'mysql'), array());
		$auth     = Config::get('auth');

		// for security, we shouldn't expose database connection to anyone.
		if (isset($database['password']) and ($password = strlen($database['password'])))
		{
			$database['password'] = str_repeat('*', $password);
		}

		$database['status'] = Runner::check_database();

		$data = array(
			'memory'   => $this->memory,
			'database' => $database,
			'auth'     => $auth,
		);

		return View::make('orchestra::installer.index', $data);
	}

	public function action_steps($step)
	{
		$data = array(
			'memory'    => $this->memory,
			'site_name' => 'Orchestra Website',
		);

		switch (intval($step))
		{
			case 1 :
				Runner::install();
				return View::make('orchestra::installer.step1', $data);
				break;

			case 2 :
				Session::flush();

				if (Runner::create_user())
				{
					return View::make('orchestra::installer.step2', $data);
				}
				else
				{
					$message = new Orchestra\Messages;
					$message->add('error', 'Unable to create user');
					return Redirect::to_action('orchestra::installer.steps', array(1))
							->with('message', serialize($message));
				}
				break;

		}
	}
}