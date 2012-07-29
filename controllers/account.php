<?php

use Orchestra\Messages, 
	Orchestra\Model\User;

class Orchestra_Account_Controller extends Orchestra\Controller
{
	/**
	 * Construct Account Controller to allow user to update own profile. Only 
	 * authenticated user should be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		
		Event::fire('orchestra.started: backend');
	}

	/**
	 * Edit User Profile Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$auth = Auth::user();

		$data = array(
			'user' => User::find($auth->id),
		);

		return View::make('orchestra::account.profile', $data);
	}

	/**
	 * POST Edit User Profile
	 *
	 * @access public
	 * @return Response
	 */
	public function post_index()
	{
		$input = Input::all();
		$rules = array(
			'email'    => array('required', 'email'),
			'fullname' => array('required'),
		);

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to(handles('orchestra::account'))
					->with_input()
					->with_errors($v);
		}

		$user           = Auth::user();
		$user->email    = $input['email'];
		$user->fullname = $input['fullname'];

		$user->save();

		$m = new Messages;
		$m->add('success', __('orchestra::response.account.profile.update'));

		return Redirect::to(handles('orchestra::account'))
				->with('message', $m->serialize());
	}

	/**
	 * Edit Password Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_password()
	{
		$auth = Auth::user();

		$data = array(
			'user' => User::find($auth->id),
		);

		return View::make('orchestra::account.password', $data);
	}

	/**
	 * POST Edit User Password
	 *
	 * @access public
	 * @return Response
	 */
	public function post_password()
	{
		$input = Input::all();
		$rules = array(
			'current_password' => array('required'),
			'new_password'     => array(
				'required', 'different:current_password'
			),
			'confirm_password' => array('same:new_password'),
		);

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to(handles('orchestra::account/password'))
					->with_input()
					->with_errors($v);
		}

		$m    = new Messages;
		$user = Auth::user();

		if (Hash::check($input['current_password'], $user->password))
		{
			$user->password = Hash::make($input['new_password']);
			$user->save();
			$m->add('success', __('orchestra::response.account.password.update'));
		}
		else
		{
			$m->add('error', __('orchestra::response.account.password.invalid'));
		}

		return Redirect::to(handles('orchestra::account/password'))
				->with('message', $m->serialize());

	}
}