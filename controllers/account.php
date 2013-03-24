<?php

use Orchestra\Presenter\Account as AccountPresenter,
	Orchestra\Site,
	Orchestra\Messages,
	Orchestra\View;

class Orchestra_Account_Controller extends Orchestra\Controller {

	/**
	 * Construct Account Controller to allow user to update own profile.
	 * Only authenticated user should be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
	}

	/**
	 * Edit User Profile Page
	 *
	 * GET (:bundle)/account
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$user = Auth::user();
		$form = AccountPresenter::form($user, handles('orchestra::account/index'));

		Event::fire('orchestra.form: user.account', array($user, $form));

		$data = array(
			'eloquent' => $user,
			'form'     => $form,
		);

		Site::set('title', __("orchestra::title.account.profile"));

		return View::make('orchestra::account.index', $data);
	}

	/**
	 * POST Edit User Profile
	 *
	 * POST (:bundle)/account
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

		if (Auth::user()->id !== $input['id']) return Response::error('500');

		Event::fire('orchestra.validate: user.account', array(& $rules));

		$msg = Messages::make();
		$val = Validator::make($input, $rules);

		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::account'))
					->with_input()
					->with_errors($val);
		}

		$user           = Auth::user();
		$user->email    = $input['email'];
		$user->fullname = $input['fullname'];

		try
		{
			$this->fire_event('updating', array($user));
			$this->fire_event('saving', array($user));

			DB::transaction(function () use ($user)
			{
				$user->save();
			});

			$this->fire_event('updated', array($user));
			$this->fire_event('saved', array($user));

			$msg->add('success', __('orchestra::response.account.profile.update'));
		}
		catch (Exception $e)
		{
			$msg->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
		}

		return Redirect::to(handles('orchestra::account'));
	}

	/**
	 * Edit Password Page
	 *
	 * GET (:bundle)/account/password
	 *
	 * @access public
	 * @return Response
	 */
	public function get_password()
	{
		$user = Auth::user();
		$form = AccountPresenter::form_password($user);
		$data = array(
			'eloquent' => $user,
			'form'     => $form,
		);

		Site::set('title', __("orchestra::title.account.password"));

		return View::make('orchestra::account.password', $data);
	}

	/**
	 * POST Edit User Password
	 *
	 * POST (:bundle)/account/password
	 *
	 * @access public
	 * @return Response
	 */
	public function post_password()
	{
		$input = Input::all();
		$rules = array(
			'current_password' => array(
				'required',
			),
			'new_password'     => array(
				'required',
				'different:current_password',
			),
			'confirm_password' => array(
				'same:new_password',
			),
		);

		if (Auth::user()->id !== $input['id']) return Response::error('500');

		$val = Validator::make($input, $rules);

		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::account/password'))
					->with_input()
					->with_errors($val);
		}

		$msg  = Messages::make();
		$user = Auth::user();

		if (Hash::check($input['current_password'], $user->password))
		{
			$user->password = $input['new_password'];

			try
			{
				DB::transaction(function () use ($user)
				{
					$user->save();
				});

				$msg->add('success', __('orchestra::response.account.password.update'));
			}
			catch (Exception $e)
			{
				$msg->add('error', __('orchestra::response.db-failed'));
			}
		}
		else
		{
			$msg->add('error', __('orchestra::response.account.password.invalid'));
		}

		return Redirect::to(handles('orchestra::account/password'));
	}

	/**
	 * Fire Event related to eloquent process
	 *
	 * @access private
	 * @param  string   $type
	 * @param  mixed    $parameters
	 * @return void
	 */
	private function fire_event($type, $parameters)
	{
		Event::fire("orchestra.{$type}: user.account", $parameters);
	}
}
