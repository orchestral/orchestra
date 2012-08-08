<?php

use Orchestra\Messages;

class Orchestra_Credential_Controller extends Orchestra\Controller
{
	/**
	 * Construct Credential Controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::not-auth')->only(array('login', 'register'));
		$this->filter('before', 'orchestra::csrf')->only(array('login', 'register'))->on(array('post'));
	}
	/**
	 * Login Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_login()
	{
		$data = array(
			'redirect' => Session::get('orchestra.redirect', handles('orchestra')),
		);

		return View::make('orchestra::credential.login', $data);
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
			return Redirect::to(handles('orchestra::login'))
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
			Event::fire('orchestra.logged.in');
			
			$m->add('success', __('orchestra::response.credential.logged-in'));

			$redirect = Input::get('redirect', handles('orchestra'));

			return Redirect::to($redirect)
					->with('message', $m->serialize());
		}
		else 
		{
			$m->add('error', __('orchestra::response.credential.invalid-combination'));

			return Redirect::to(handles('orchestra::login'))
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

		Event::fire('orchestra.logged.out');

		$m = new Messages;
		$m->add('success', __('orchestra::response.credential.logged-out'));
		
		return Redirect::to(handles('orchestra::login'))
				->with('message', $m->serialize());
	}
}