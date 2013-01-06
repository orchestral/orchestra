<?php

use Orchestra\Mail,
	Orchestra\Messages,
	Orchestra\View,
	Orchestra\Model\User,
	Orchestra\Presenter\Account as AccountPresenter;

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

		$this->filter('before', 'orchestra::allow-registration')
			->only(array('register'));

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
		))->with('_title_', __('orchestra::title.login'));
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
			$user = Auth::user();

			// Verify the user account if has not been verified.
			if ((int) $user->status === User::UNVERIFIED)
			{
				$user->status = User::VERIFIED;
				$user->save();
			}

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
		$user = new User;
		$form = AccountPresenter::form($user, handles('orchestra::register'));
		
		$form->extend(function ($form)
		{
			$form->hidden('redirect', function ($field)
			{
				$field->value = handles('orchestra::login');
			});

			$form->token = true;
		});
		
		return View::make('orchestra::credential.register', array(
			'eloquent' => $user,
			'form'     => $form,
		))->with('_title_', __('orchestra::title.register'));
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
		$input    = Input::all();
		$password = Str::random(5);
		$rules    = array(
			'email'    => array('required', 'email', 'unique:users,email'),
			'fullname' => array('required'),
		);

		Event::fire('orchestra.validate: user.account', array(& $rules));
	
		$msg  = new Messages;
		$val = Validator::make($input, $rules);
	
		// Validate user registration, if any errors is found redirect it 
		// back to registration page with the errors
		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::register'))
					->with_input()
					->with_errors($val);
		}

		$user = new User(array(
			'email'    => $input['email'],
			'fullname' => $input['fullname'],
			'password' => $password,
		));

		try
		{
			$this->fire_event('creating', $user);
			$this->fire_event('saving', $user);

			DB::transaction(function () use ($user)
			{
				$user->save();
				$user->roles()->sync(array(
					Config::get('orchestra::orchestra.member_role', 2)
				));
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

		return $this->send_email($user, $password, $msg);
	}

	protected function send_email(User $user, $password, Messages $msg)
	{

		$site     = Orchestra\Core::memory()->get('site.name', 'Orchestra');
		$data     = array(
			'password' => $password,
			'user'     => $user,
			'site'     => $site,
		);

		$mailer = Mail::send('orchestra::email.credential.register', $data,
			function ($mail) use ($data, $user, $site)
			{
				$mail->subject(__('orchestra::email.credential.register', compact('site'))->get())
					->to($user->email, $user->fullname)
					->send();
			});

		if( ! $mailer->was_sent($user->email))
		{
			$msg->add('error', __('orchestra::response.credential.register.email-fail'));
		}
		else
		{
			$msg->add('success', __('orchestra::response.credential.register.email-send'));
		}

		return Redirect::to(handles('orchestra::login'))
				->with('message', $msg->serialize());
	}

	/**
	 * Fire Event related to eloquent process
	 *
	 * @access private
	 * @param  string   $type
	 * @param  Eloquent $user
	 * @return void
	 */
	private function fire_event($type, $user)
	{
		Event::fire("orchestra.{$type}: user.account", array($user));
	}
}
