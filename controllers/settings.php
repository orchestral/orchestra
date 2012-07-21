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

		Event::fire('orchestra.started: manage');
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
			'site_name'        => $memory->get('site.name', ''),
			'site_description' => $memory->get('site.description', ''),
			'email_default'  => $memory->get('email.default', ''),
		));

		Form::of('orchestra.settings', function ($form) use ($settings)
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
				$fieldset->control('select', 'Transport', function ($control)
				{
					$control->name    = 'email_default';
					$control->options = array(
						'mail'     => 'Mail',
						'smtp'     => 'SMTP',
						'sendmail' => 'Sendmail',
					);
				});
			});
		});

		Event::fire('orchestra.form: settings', array($settings));

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
			'site_name'     => array('required'),
			'email_default' => array('required'),
		);

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

		$m = Messages::make('success', __('orchestra::response.settings.updated'));

		return Redirect::to(handles('orchestra::settings'))
				->with('message', $m->serialize());
	}
}