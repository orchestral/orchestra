<?php

use Orchestra\Core,
	Orchestra\Mail,
	Orchestra\Messages,
	Orchestra\Model\User,
	Orchestra\Site,
	Orchestra\View;

class Orchestra_Forgot_Controller extends Orchestra\Controller {

	/**
	 * Construct Forgot Password Controller with some pre-define
	 * configuration
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::not-auth');
		$this->filter('before', 'orchestra::csrf')
			->only(array('index'))
			->on(array('post'));
	}

	/**
	 * Show Forgot Password Page where user can enter their current e-mail
	 * address.
	 *
	 * GET (:bundle)/forgot
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		Site::set('title', __('orchestra::title.forgot-password'));

		return View::make('orchestra::forgot.index');
	}

	/**
	 * Validate requested e-mail address for password reset, we should first
	 * send a URL where user need to visit before the system can actually
	 * change the password on their behave.
	 *
	 * POST (:bundle)/forgot
	 *
	 * @access public
	 * @return Response
	 */
	public function post_index()
	{
		$input = Input::all();
		$rules = array(
			'email' => array('required', 'email'),
		);

		$msg = Messages::make();
		$val = Validator::make($input, $rules);

		if ($val->fails())
		{
			// If any of the validation is not properly formatted, we need
			// to tell it the the user. This might not be important but a
			// good practice to make sure all form use the same e-mail
			// address validation
			return Redirect::to(handles('orchestra::forgot'))
					->with_input()
					->with_errors($val);
		}

		$user = User::where_email($input['email'])->first();

		if (is_null($user))
		{
			// no user could be associated with the provided email address
			$msg->add('error', __('orchestra::response.db-404'));

			return Redirect::to(handles('orchestra::forgot'));
		}

		$meta   = Orchestra\Memory::make('user');
		$memory = Core::memory();
		$hash   = sha1($user->email.Str::random(10));
		$url    = handles('orchestra::forgot/reset/'.$user->id.'/'.$hash);
		$site   = $memory->get('site.name', 'Orchestra');
		$data   = array(
			'user' => $user,
			'url'  => $url,
			'site' => $site,
		);

		$mailer = Mail::send('orchestra::email.forgot.request', $data,
			function ($mail) use ($data, $user, $site)
			{
				$mail->subject(__('orchestra::email.forgot.request', compact('site'))->get())
					->to($user->email, $user->fullname);
			});

		if( ! $mailer->was_sent($user->email))
		{
			$msg->add('error', __('orchestra::response.forgot.email-fail'));
		}
		else
		{
			$meta->put("reset_password_hash.{$user->id}", $hash);

			$msg->add('success', __('orchestra::response.forgot.email-send'));
		}

		return Redirect::to(handles('orchestra::forgot'));
	}

	/**
	 * Once user actually visit the reset my password page, we now should be
	 * able to make the operation to create a temporary password on behave
	 * of the user
	 *
	 * GET (:bundle)/forgot/reset/(:id)/(:hash)
	 *
	 * @access public
	 * @param  int      $user_id
	 * @param  string   $hash
	 * @return Response
	 */
	public function get_reset($user_id, $hash)
	{
		if ( ! (is_numeric($user_id) and is_string($hash)) or empty($hash))
		{
			return Response::error('404');
		}

		$user = User::find($user_id);
		$meta = Orchestra\Memory::make('user');

		if (is_null($user) or $hash !== $meta->get("reset_password_hash.{$user_id}"))
		{
			return Response::error('404');
		}

		$msg      = Messages::make();
		$memory   = Core::memory();
		$hash     = sha1($user->email.Str::random(10));
		$password = Str::random(5);
		$site     = $memory->get('site.name', 'Orchestra');
		$data     = array(
			'password' => $password,
			'user'     => $user,
			'site'     => $site,
		);

		$mailer = Mail::send('orchestra::email.forgot.reset', $data,
			function ($mail) use ($data, $user, $site)
			{
				$mail->subject(__('orchestra::email.forgot.reset', compact('site'))->get())
					->to($user->email, $user->fullname);
			});

		if( ! $mailer->was_sent($user->email))
		{
			$msg->add('error', __('orchestra::response.forgot.email-fail'));
		}
		else
		{
			$meta->put("reset_password_hash.{$user_id}", "");

			$user->password = $password;
			$user->save();

			$msg->add('success', __('orchestra::response.forgot.email-send'));
		}

		return Redirect::to(handles('orchestra::login'));
	}
}
