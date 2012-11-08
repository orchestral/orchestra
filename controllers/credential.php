<?php

use Orchestra\Messages, 
	Orchestra\View;

class Orchestra_Credential_Controller extends Orchestra\Controller
{
	private $username = null;

	/**
	 * Construct Credential Controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->username = (array) Config::get('auth.username');

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
		$redirect = Session::get('orchestra.redirect', handles('orchestra'));
		$username = current($this->username);

		return View::make('orchestra::credential.login', compact('redirect', 'username'));
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
			'username' => array('required'),
			'password' => array('required'),
		);

		$m = new Messages;
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
			'username' => $input['username'], 
			'password' => $input['password']
		);

		// We should now attempt to login the user using Auth class, 
		if (Auth::attempt($attempt))
		{
			Event::fire('orchestra.logged.in');
			
			$m->add('success', __('orchestra::response.credential.logged-in'));

			$redirect = Input::get('redirect', handles('orchestra'));

			return Redirect::to($redirect)
					->with('message', $m->serialize());
		}

		$m->add('error', __('orchestra::response.credential.invalid-combination'));

		return Redirect::to(handles('orchestra::login'))
				->with('message', $m->serialize());
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