<?php

use Laravel\Fluent, 
	Orchestra\Core,
	Orchestra\Form,
	Orchestra\Messages;

class Orchestra_Settings_Controller extends Orchestra\Controller 
{
	/**
	 * Construct Settings Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		$this->filter('before', 'orchestra::manage');

		Event::fire('orchestra.started: backend');
	}

	/**
	 * Orchestra Settings Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		// Orchestra settings are stored using Hybrid\Memory, we need to fetch 
		// it and convert it to Fluent (to mimick Eloquent properties).
		$memory   = Core::memory();
		$settings = new Fluent(array(
			'site_name'              => $memory->get('site.name', ''),
			'site_description'       => $memory->get('site.description', ''),
			'email_default'          => $memory->get('email.default', ''),
			'email_smtp_host'        => $memory->get('email.transports.smtp.host', ''),
			'email_smtp_port'        => $memory->get('email.transports.smtp.port', ''),
			'email_smtp_username'    => $memory->get('email.transports.smtp.username', ''),
			'email_smtp_password'    => $memory->get('email.transports.smtp.password', ''),
			'email_smtp_encryption'  => $memory->get('email.transports.smtp.encryption', ''),
			'email_sendmail_command' => $memory->get('email.transports.sendmail.command', ''),
		));

		$form = Form::of('orchestra.settings', function ($form) use ($settings)
		{
			$form->row($settings);	
			$form->attr(array(
				'action' => handles('orchestra::settings'),
				'method' => 'POST',
			));

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'Site Name', 'site_name');
				$fieldset->control('textarea', 'Site Description', function ($control) {
					$control->name = 'site_description';
					$control->attr = array('rows' => 3);
				});
			});

			$form->fieldset('E-mail and Messaging', function ($fieldset)
			{
				$fieldset->control('select', 'E-mail Transport', function ($control)
				{
					$control->name    = 'email_default';
					$control->options = array(
						'mail'     => 'Mail',
						'smtp'     => 'SMTP',
						'sendmail' => 'Sendmail',
					);
				});

				$fieldset->control('input:text', 'SMTP Host', 'email_smtp_host');
				$fieldset->control('input:text', 'SMTP Port', 'email_smtp_port');
				$fieldset->control('input:text', 'SMTP Username', 'email_smtp_username');
				$fieldset->control('input:password', 'SMTP Password', 'email_smtp_password');
				$fieldset->control('input:text', 'STMP Encryption', 'email_smtp_encryption');
				$fieldset->control('input:text', 'Sendmail Command', 'email_sendmail_command');
			});
		});

		Event::fire('orchestra.form: settings', array($settings, $form));

		$data = array(
			'eloquent'      => $settings,
			'form'          => Form::of('orchestra.settings'),
			'resource_name' => __('orchestra::title.settings.list')->get(),
		);

		return View::make('orchestra::resources.edit', $data);
	}

	public function post_index()
	{
		$input = Input::all();
		$rules = array(
			'site_name'       => array('required'),
			'email_default'   => array('required'),
			'email_smtp_port' => array('numeric'),
		);

		Event::fire('orchestra.validate: settings', array(& $rules));

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to(handles('orchestra::settings'))
					->with_input()
					->with_errors($v);
		}

		$memory = Core::memory();

		$memory->put('site.name', $input['site_name']);
		$memory->put('site.description', $input['site_description']);
		$memory->put('email.default', $input['email_default']);
		$memory->put('email.transports.smtp.host', $input['email_smtp_host']);
		$memory->put('email.transports.smtp.port', $input['email_smtp_port']);
		$memory->put('email.transports.smtp.username', $input['email_smtp_username']);
		$memory->put('email.transports.smtp.password', $input['email_smtp_password']);
		$memory->put('email.transports.smtp.encryption', $input['email_smtp_encryption']);
		$memory->put('email.transports.sendmail.command', $input['email_sendmail_command']);

		Event::fire('orchestra.save: settings', array($memory));

		$m = Messages::make('success', __('orchestra::response.settings.update'));

		return Redirect::to(handles('orchestra::settings'))
				->with('message', $m->serialize());
	}
}