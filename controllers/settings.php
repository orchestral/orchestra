<?php

use Laravel\Fluent,
	Orchestra\Core,
	Orchestra\Extension,
	Orchestra\Messages,
	Orchestra\Presenter\Setting as SettingPresenter,
	Orchestra\Site,
	Orchestra\View;

class Orchestra_Settings_Controller extends Orchestra\Controller {

	/**
	 * Construct Settings Controller, only authenticated user should be able
	 * to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		$this->filter('before', 'orchestra::manage');
	}

	/**
	 * Orchestra Settings Page
	 *
	 * GET (:bundle)/settings
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		// Orchestra settings are stored using Orchestra\Memory, we need to
		// fetch it and convert it to Fluent (to mimick Eloquent properties).
		$memory   = Core::memory();

		$settings = new Fluent(array(
			'site_name'              => $memory->get('site.name', ''),
			'site_description'       => $memory->get('site.description', ''),
			'site_user_registration' => ($memory->get('site.users.registration', false) ? 'yes' : 'no'),

			'email_default'          => $memory->get('email.default', ''),
			'email_from'             => $memory->get('email.from', ''),
			'email_smtp_host'        => $memory->get('email.transports.smtp.host', ''),
			'email_smtp_port'        => $memory->get('email.transports.smtp.port', ''),
			'email_smtp_username'    => $memory->get('email.transports.smtp.username', ''),
			'email_smtp_password'    => $memory->get('email.transports.smtp.password', ''),
			'email_smtp_encryption'  => $memory->get('email.transports.smtp.encryption', ''),
			'email_sendmail_command' => $memory->get('email.transports.sendmail.command', ''),
		));

		$form = SettingPresenter::form($settings);

		Event::fire('orchestra.form: settings', array($settings, $form));

		Site::set('title', __('orchestra::title.settings.list'));

		$data = array(
			'eloquent' => $settings,
			'form'     => $form,
		);

		return View::make('orchestra::settings.index', $data);
	}

	/**
	 * POST Orchestra Settings Page
	 *
	 * POST (:bundle)/settings
	 *
	 * @access public
	 * @return Response
	 */
	public function post_index()
	{
		$input = Input::all();
		$rules = array(
			'site_name'       => array('required'),
			'email_default'   => array('required', 'in:mail,smtp,sendmail'),
			'email_smtp_port' => array('numeric'),
		);

		isset($input['email_default']) or $input['email_default'] = 'mail';


		switch ($input['email_default'])
		{
			case 'smtp' :
				$input['email_from']          = $input['email_smtp_username'];
				$rules['email_smtp_username'] = array('required', 'email');
				$rules['email_smtp_host']     = array('required');
				break;

			case 'sendmail' :
				$rules['email_sendmail_command'] = array('required');
			default :
				$rules['email_from'] = array('required', 'email');
				break;
		}

		Event::fire('orchestra.validate: settings', array(& $rules));

		$val = Validator::make($input, $rules);

		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::settings'))
					->with_input()
					->with_errors($val);
		}

		$memory = Core::memory();

		$memory->put('site.name', $input['site_name']);
		$memory->put('site.description', $input['site_description']);
		$memory->put('site.users.registration', ($input['site_user_registration'] === 'yes'));
		$memory->put('email.default', $input['email_default']);

		$memory->put('email.from', $input['email_from']);

		if ((empty($input['email_smtp_password']) and $input['stmp_change_password'] === 'no'))
		{
			$input['email_smtp_password'] = $memory->get('email.transports.smtp.password');	
		}
		
		$memory->put('email.transports.smtp.host', $input['email_smtp_host']);
		$memory->put('email.transports.smtp.port', $input['email_smtp_port']);
		$memory->put('email.transports.smtp.username', $input['email_smtp_username']);
		$memory->put('email.transports.smtp.password', $input['email_smtp_password']);
		$memory->put('email.transports.smtp.encryption', $input['email_smtp_encryption']);
		$memory->put('email.transports.sendmail.command', $input['email_sendmail_command']);

		Event::fire('orchestra.saved: settings', array($memory, $input));

		Messages::make()
				->add('success', __('orchestra::response.settings.update'));

		return Redirect::to(handles('orchestra::settings'));
	}
}
