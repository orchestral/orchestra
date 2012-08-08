<?php 

class Orchestra_Forgot_Controller extends Orchestra\Controller 
{
	/**
	 * Construct Forgot Password Controller with some pre-define configuration 
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::not-auth');
		$this->filter('before', 'orchestra::csrf')->only(array('index'))->on(array('post'));
	}

	/**
	 * Show Forgot Password Page where user can enter their current e-mail address
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		return View::make('orchestra::forgot.index');
	}

	/**
	 * Validate requested e-mail address for password reset, we should first send 
	 * a URL where user need to visit before the system can actually change the 
	 * password on their behave. 
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

		$m = new Orchestra\Messages;
		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			// If any of the validation is not properly formatted, we need to
			// tell it the the user. This might not be important but a good 
			// practice to make sure all form use the same e-mail address validation
			return Redirect::to(handles('orchestra::forgot'))
					->with_input()
					->with_errors($v);
		}

		$user = Orchestra\Model\User::where_email($input['email'])->first();

		if (is_null($user))
		{
			// no user could be associated with the provided email address
			$m->add('error', __('orchestra::response.db-404'));

			return Redirect::to(handles('orchestra::forgot'))
					->with('message', $m->serialize());
		}

		$meta   = Orchestra\Model\User\Meta::where('user_id', '=', $user->id)
					->where('name', '=', 'reset_password_hash')
					->first();

		if (is_null($meta))
		{
			$meta = new Orchestra\Model\User\Meta(array(
				'user_id' => $user->id,
				'name'    => 'reset_password_hash',
			));
		}

		$memory  = Orchestra\Core::memory();
		$hash    = sha1($user->email.Str::random(10));
		$url     = handles('orchestra::forgot/reset/'.$user->id.'/'.$hash);
		$site    = $memory->get('site.name', 'Orchestra');
		$subject = __('orchestra::email.forgot.subject', array('site' => $site))->get();
		$message = __('orchestra::email.forgot.message', array('fullname' => $user->fullname, 'url' => $url))->get();
		
		$mailer  = IoC::resolve('orchestra.mailer');
		$mailer->to($user->email, $user->fullname)
			->subject($subject)
			->body($message)
			->send();

		if( ! $mailer->was_sent($user->email))
		{
			$m->add('error', __('orchestra::response.forgot.fail'));
		}
		else 
		{
			$meta->value = $hash;
			$meta->save();

			$m->add('success', __('orchestra::response.forgot.send'));
		}
		
		return Redirect::to(handles('orchestra::forgot'))
			->with('message', $m->serialize());
	}

	/**
	 * Once user actually visit the reset my password page, we now should be able to 
	 * make the operation to create a temporary password on behave of the user
	 *
	 * @access public
	 * @param  int      $id
	 * @param  string   $hash
	 * @return Response      
	 */
	public function get_reset($id, $hash)
	{
		if ( ! (is_numeric($id) and is_string($hash)) or empty($hash))
		{
			return Response::error('404');
		}

		$meta = Orchestra\Model\User\Meta::where('user_id', '=', $id)
					->where('name', '=', 'reset_password_hash')
					->where('value', '=', $hash)
					->first();

		if (is_null($meta)) return Response::error('404');
		
		$m        = new Orchestra\Messages;
		$user     = $meta->users()->first();

		$memory   = Orchestra\Core::memory();
		$hash     = sha1($user->email.Str::random(10));
		$password = Str::random(5);
		$site     = $memory->get('site.name', 'Orchestra');
		$subject  = __('orchestra::email.reset.subject', array('site' => $site))->get();
		$message  = __('orchestra::email.reset.message', array('fullname' => $user->fullname, 'password' => $password))->get();
		
		$mailer  = IoC::resolve('orchestra.mailer');
		$mailer->to($user->email, $user->fullname)
			->subject($subject)
			->body($message)
			->send();

		if( ! $mailer->was_sent($user->email))
		{
			$m->add('error', __('orchestra::response.forgot.fail'));
		}
		else 
		{
			$meta->value = '';
			$meta->save();

			$user->password = Hash::make($password);
			$user->save();

			$m->add('success', __('orchestra::response.forgot.send'));
		}
		
		return Redirect::to(handles('orchestra::login'))
			->with('message', $m->serialize());
	}
}