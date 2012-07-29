<?php 

use Laravel\Fluent, Orchestra\Core, Orchestra\Extension, 
	Orchestra\Form, Orchestra\Messages;

class Orchestra_Extensions_Controller extends Orchestra\Controller 
{
	/**
	 * Construct Extensions Controller, only authenticated user should 
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
	 * List all available extensions
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$data = array(
			'extensions' => Extension::detect(),
		);

		return View::make('orchestra::extensions.index', $data);
	}

	/**
	 * Activate an extension
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_activate($name = null)
	{
		if (is_null($name) or Extension::started($name)) return Event::first('404');

		Extension::activate($name);

		$m = new Messages;
		$m->add('success', __('orchestra::response.extensions.activate', array('name' => $name)));

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}

	/**
	 * Deactivate an extension
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_deactivate($name = null)
	{
		if (is_null($name) or ! Extension::started($name)) return Event::first('404');

		Extension::deactivate($name);

		$m = new Messages;
		$m->add('success', __('orchestra::response.extensions.deactivate', array('name' => $name)));

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}

	/**
	 * Configure an extension
	 * 
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_configure($name = null)
	{
		if (is_null($name) or ! Extension::started($name)) return Event::first('404');

		if (Extension::option($name, 'configurable') === false) return Event::first('404');

		// Load configuration from memory.
		$memory = Core::memory();
		$config = new Fluent((array) $memory->get("extension_{$name}", array()));

		// Add basic form, allow extension to add custom configuration field to this
		$form = Form::of("orchestra.extension: {$name}", function ($form) use ($name, $config)
		{
			$form->row($config);

			$form->attr(array(
				'action' => handles("orchestra::extensions/configure/{$name}"),
				'method' => "POST",
			));

			$handles = Extension::option($name, 'handles');

			// We should only cater for custom URL handles for a route.
			if ( ! is_null($handles))
			{
				$form->fieldset(function ($fieldset) use ($handles)
				{
					$fieldset->control('input:text', 'handles', function ($control) use ($handles)
					{
						$control->label = 'Handle URL';
						$control->value = $handles;
					});
				});
			}
		});

		// Now lets the extension do their magic.
		Event::fire("orchestra.form: extension.{$name}", array($config, $form));

		$data = array(
			'eloquent'      => $config,
			'form'          => Form::of("orchestra.extension: {$name}"),
			'resource_name' => __("orchestra::title.extensions.configure", array('name' => $name))->get(),
		);

		return View::make('orchestra::resources.edit', $data);
	}

	/**
	 * Update extension configuration
	 * 
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function post_configure($name = null)
	{
		if (is_null($name) or ! Extension::started($name)) return Event::first('404');

		$input  = Input::all();
		$memory = Core::memory();
		$config = new Fluent((array) $memory->get("extension_{$name}", array()));
		$loader  = (array) $memory->get("extensions.active.{$name}", array());
		
		// This part should be part of extension loader configuration. What saved here
		// wouldn't be part of extension configuration.
		if ( isset($input['handles']) and ! empty($input['handles']))
		{
			$loader['handles'] = $input['handles'];
			unset($input['handles']);

			$memory->put("extensions.active.{$name}", $loader);
		}

		// In any event where extension need to do some custom handling.
		Event::fire("orchestra.save: extension.{$name}", array($config));

		$memory->put("extension_{$name}", $input);

		$m = new Messages;
		$m->add('success', __("orchestra::response.extensions.configure", array('name' => $name)));

		return Redirect::to(handles('orchestra::extensions'))
			->with('message', $m->serialize());
	}
}