<?php

class Orchestra_Credential_Controller extends Orchestra\Controller
{
	public $restful = true;

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
		
		return Redirect::to('orchestra/login')
				->with('message', Orchestra\Messages::make('success', 'You have been logged out')->serialize());
	}
}