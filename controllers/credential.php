<?php

class Orchestra_Credential_Controller extends Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::installed');

		View::share('memory', Orchestra\Core::memory());
	}

	public function get_login()
	{
		return View::make('orchestra::credential.login');
	}

	public function post_login()
	{
		$input = Input::all();
		$rules = array(
			'email'    => array('required', 'email'),
			'password' => array('required'),
		);

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to('orchestra/login')
					->with_input()
					->with_errors($v);
		}

		if (Auth::attempt(array('username' => $input['email'], 'password' => $input['password'])))
		{
			return Redirect::to('orchestra')
					->with('message', Orchestra\Messages::make('success', 'User has been logged in')->serialize());
		}
		else 
		{
			return Redirect::to('orchestra/login')
					->with('message', Orchestra\Messages::make('error', 'Invalid e-mail address and password combination')->serialize());
		}

	}

	public function get_logout()
	{
		Auth::logout();
		
		return $this->get_login();
	}
}