<?php

use Orchestra\Messages;

class Orchestra_Credential_Controller extends Orchestra\Controller
{
	/**
	 * Login Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_login()
	{
		return View::make('orchestra::credential.login');
	}

	/**
	 * POST Login
	 *
	 * @access public
	 * @return Response
	 */
	public function post_login()
	{
		$input = Input::all();
		$rules = array(
			'email'    => array('required', 'email'),
			'password' => array('required'),
		);

		$v = Validator::make($input, $rules);

		// Validate user login, if any errors is found redirect it back to 
		// login page with the errors
		if ($v->fails())
		{
			return Redirect::to('orchestra/login')
					->with_input()
					->with_errors($v);
		}

		$attempt = array(
			'username' => $input['email'], 
			'password' => $input['password']
		);

		$m = new Messages;

		// We should now attempt to login the user using Auth class, 
		if (Auth::attempt($attempt))
		{
			$m->add('success', 'User has been logged in');

			return Redirect::to('orchestra')
					->with('message', $m->serialize());
		}
		else 
		{
			$m->add('error', 'Invalid e-mail address and password combination');

			return Redirect::to('orchestra/login')
					->with('message', $m->serialize());
		}

	}

	/**
	 * Logout the user
	 *
	 * @access public
	 * @return Response
	 */
	public function get_logout()
	{
		Auth::logout();

		$m = new Messages;
		$m->add('success', 'You have been logged out');
		
		return Redirect::to('orchestra/login')
				->with('message', $m->serialize());
	}
}