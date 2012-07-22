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
	}

	public function get_index()
	{
		return View::make('orchestra::forgot.index');
	}

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
}