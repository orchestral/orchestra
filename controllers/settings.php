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
			'site_name'        => $memory->get('site_name'),
			'site_description' => $memory->get('site_description'),
		));

		Form::of('orchestra.settings', function ($form) use ($settings)
		{
			$form->row($settings);	
			$form->attr(array(
				'action' => URL::to('orchestra/settings'),
				'method' => 'POST',
			));

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'Site Name', 'site_name');
				$fieldset->control('textarea', 'Site Description', 'site_description');
			});
		});

		Event::fire('orchestra.form: settings', array($settings));

		$data = array(
			'eloquent'      => $settings,
			'form'          => Form::of('orchestra.settings'),
			'resource_name' => 'Settings',
		);

		return View::make('orchestra::resources.edit', $data);
	}

	public function post_index()
	{
		$input = Input::all();
		$rules = array(
			'site_name' => array('required'),
		);

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to('orchestra/settings')
					->with_input()
					->with_errors($v);
		}

		$memory = Core::memory();

		$memory->put('site_name', $input['site_name']);
		$memory->put('site_description', $input['site_description']);

		$m = Messages::make('success', __('orchestra::response.settings.updated'));

		return Redirect::to('orchestra/settings')
				->with('message', $m->serialize());
	}
}