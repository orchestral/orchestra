<?php

use Orchestra\Messages,
	Orchestra\View,
	Orchestra\Model\User;

class Orchestra_Credential_Controller extends Orchestra\Controller {

	/**
	 * List of auth.username configuration value.
	 *
	 * @var mixed
	 */
	private $username_types = null;

	/**
	 * Construct Credential Controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->username_types = (array) Config::get('auth.username');

		$this->filter('before', 'orchestra::not-auth')
			->only(array('login', 'register'));

		$this->filter('before', 'orchestra::csrf')
			->only(array('login', 'register'))
			->on(array('post'));
	}

	/**
	 * Login Page
	 *
	 * GET (:bundle)/login
	 *
	 * @access public
	 * @return Response
	 */
	public function get_login()
	{
		$redirect       = Session::get('orchestra.redirect', handles('orchestra'));
		$username_types = current($this->username_types);

		return View::make('orchestra::credential.login', compact(
			'redirect',
			'username_types'
		));
	}

	/**
	 * Register Page
	 *
	 * GET (:bundle)/register
	 *
	 * @access public
	 * @return Response
	 */
	public function get_register()
	{
		$redirect       = handles('orchestra::login');
		$username_types = current($this->username_types);

		return View::make('orchestra::credential.register', compact(
			'redirect',
			'username_types'
		));
	}

	/**
	 * POST Register
	 *
	 * POST (:bundle)/register
	 *
	 * @access public
	 * @return Response
	 */
	public function post_register()
	{
		$input = Input::all();
		$rules = array(
				'username' => array('required', 'email'),
				'password' => array('required'),
		);
	
		$msg = new Messages;
		$val = Validator::make($input, $rules);
	
		// Validate user login, if any errors is found redirect it back to
		// login page with the errors
		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::register'))
			->with_input()
			->with_errors($val);
		}
	
		$type = 'update';
		$user = User::where_email($input['username'])->first();

		if (!is_null($user))
		{
			$msg->add('error', __("orchestra::response.users.exists"));
			return Redirect::to(handles('orchestra::register'))
					->with('message', $msg->serialize());
		}
		

		$user = new User(array(
				'password' => $input['password'] ?: '',
		));
		
		$user->email    = $input['username'];

		try
		{
			$this->fire_event('creating', $user);
			$this->fire_event('saving', $user);

			DB::transaction(function () use ($user, $input)
			{
				$user->save();
				$user->roles()->sync(array(2)); // register as member
			});

			$this->fire_event('created', $user);
			$this->fire_event('saved', $user);

			$msg->add('success', __("orchestra::response.users.create"));
		}
		catch (Exception $e)
		{
			$msg->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
			return Redirect::to(handles('orchestra::register'))
					->with('message', $msg->serialize());
		}

		return Redirect::to(handles('orchestra::login'))
				->with('message', $msg->serialize());
		
	}
	
	/**
	 * POST Login
	 *
	 * POST (:bundle)/login
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

		$msg = new Messages;
		$val = Validator::make($input, $rules);

		// Validate user login, if any errors is found redirect it back to
		// login page with the errors
		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::login'))
					->with_input()
					->with_errors($val);
		}

		$attempt = array(
			'username' => $input['username'],
			'password' => $input['password'],
			'remember' => (isset($input['remember']) and $input['remember'] === 'yes'),
		);

		// We should now attempt to login the user using Auth class.
		if (Auth::attempt($attempt))
		{
			Event::fire(array('orchestra.logged.in', 'orchestra.auth: login'));

			$msg->add('success', __('orchestra::response.credential.logged-in'));

			$redirect = Input::get('redirect', handles('orchestra'));

			return Redirect::to($redirect)
					->with('message', $msg->serialize());
		}

		$msg->add('error', __('orchestra::response.credential.invalid-combination'));

		return Redirect::to(handles('orchestra::login'))
				->with('message', $msg->serialize());
	}

	/**
	 * Logout the user
	 *
	 * GET (:bundle)/logout
	 *
	 * @access public
	 * @return Response
	 */
	public function get_logout()
	{
		$redirect = Input::get('redirect', handles('orchestra::login'));

		Auth::logout();

		Event::fire(array('orchestra.logged.out', 'orchestra.auth: logout'));

		$msg = Messages::make('success', __('orchestra::response.credential.logged-out'));

		return Redirect::to($redirect)
				->with('message', $msg->serialize());
	}
}
