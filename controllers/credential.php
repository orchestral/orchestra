<?php

use Orchestra\Mail,
	Orchestra\Messages,
	Orchestra\Site,
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

		Site::set('title', __("orchestra::title.login"));

		return View::make('orchestra::credential.login', compact(
			'redirect',
			'username_types'
		));
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

		$msg = Messages::make();
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

			$redirect = Input::get('redirect', handles('orchestra'));

			$msg->add('success', __('orchestra::response.credential.logged-in'));

			return Redirect::to($redirect);
		}

		$msg->add('error', __('orchestra::response.credential.invalid-combination'));

		return Redirect::to(handles('orchestra::login'));
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

		$msg = Messages::make();
		$msg->add('success', __('orchestra::response.credential.logged-out'));

		return Redirect::to($redirect);
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
		if ( ! IoC::registered('orchestra.user: register'))
		{
			IoC::register('orchestra.user: register', function ()
			{
				return new User;
			});
		}

		$user  = IoC::resolve('orchestra.user: register');
		$title = 'orchestra::title.register';
		$form  = AccountPresenter::form($user, handles('orchestra::register'));
		
		$form->extend(function ($form) use ($title)
		{
			$form->submit_button = $title;

			$form->hidden('redirect', function ($field)
			{
				$field->value = handles('orchestra::login');
			});

			$form->token = true;
		});

		Event::fire('orchestra.form: user.account', array($user, $form));
		
		Site::set('title', __($title));
		
		return View::make('orchestra::credential.register', array(
			'eloquent' => $user,
			'form'     => $form,
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
		if ( ! IoC::registered('orchestra.user: register'))
		{
			IoC::register('orchestra.user: register', function ()
			{
				return new User;
			});
		}

		$input    = Input::all();
		$password = Str::random(5);
		$rules    = array(
			'email'    => array('required', 'email', 'unique:users,email'),
			'fullname' => array('required'),
		);

		Event::fire('orchestra.validate: user.account', array(& $rules));
	
		$msg = Messages::make();
		$val = Validator::make($input, $rules);
	
		// Validate user registration, if any errors is found redirect it 
		// back to registration page with the errors
		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::register'))
					->with_input()
					->with_errors($val);
		}

		$user = IoC::resolve('orchestra.user: register');
		$user->fill(array(
			'email'    => $input['email'],
			'fullname' => $input['fullname'],
			'password' => $password,
		));

		try
		{
			$this->fire_event('creating', array($user));
			$this->fire_event('saving', array($user));

			DB::transaction(function () use ($user)
			{
				$user->save();
				$user->roles()->sync(array(
					Config::get('orchestra::orchestra.member_role', 2)
				));
			});

			$this->fire_event('created', array($user));
			$this->fire_event('saved', array($user));

			$msg->add('success', __("orchestra::response.users.create"));
		}
		catch (Exception $e)
		{
			$msg->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
			
			return Redirect::to(handles('orchestra::register'));
		}

		return $this->send_email($user, $password, $msg);
	}

	/**
	 * Send new registration e-mail to user.
	 *
	 * @access protected
	 * @param  User     $user
	 * @param  string   $password
	 * @param  Messages $msg
	 * @return Response
	 */
	protected function send_email(User $user, $password, Messages $msg)
	{
		$site = Orchestra\Core::memory()->get('site.name', 'Orchestra');
		$data = array(
			'password' => $password,
			'user'     => $user,
			'site'     => $site,
		);

		$mailer = Mail::send('orchestra::email.credential.register', $data,
			function ($mail) use ($data, $user, $site)
			{
				$mail->subject(__('orchestra::email.credential.register', compact('site'))->get())
					->to($user->email, $user->fullname);
			});

		if( ! $mailer->was_sent($user->email))
		{
			$msg->add('error', __('orchestra::response.credential.register.email-fail'));
		}
		else
		{
			$msg->add('success', __('orchestra::response.credential.register.email-send'));
		}

		return Redirect::to(handles('orchestra::login'));
	}

	/**
	 * Fire Event related to eloquent process
	 *
	 * @access private
	 * @param  string   $type
	 * @param  array    $parameters
	 * @return void
	 */
	private function fire_event($type, $parameters)
	{
		Event::fire("orchestra.{$type}: user.account", $parameters);
	}
}
