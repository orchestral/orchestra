<?php

use Laravel\Fluent,
	Orchestra\Core,
	Orchestra\Extension,
	Orchestra\Form,
	Orchestra\Messages,
	Orchestra\View;

class Orchestra_Extensions_Controller extends Orchestra\Controller {

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
	}

	/**
	 * List all available extensions
	 *
	 * GET (:bundle)/extensions
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$data = array(
			'extensions' => Extension::detect(),
			'_title_' => __("orchestra::title.extensions.list")->get(),
		);

		foreach($data['extensions'] as $name => &$extension)
		{
			$extension->require    = (array)$extension->require;
			$extension->unresolved = Extension::not_activatable($name);
		}

		return View::make('orchestra::extensions.index', $data);
	}

	/**
	 * Activate an extension
	 *
	 * GET (:bundle)/extensions/activate/(:name)
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_activate($name = null)
	{
		if (is_null($name) or Extension::started($name)) return Event::first('404');

		$m = new Messages;

		try
		{
			Extension::activate($name);
			$m->add('success', __('orchestra::response.extensions.activate', array('name' => $name)));
		}
		catch (Orchestra\Extension\UnresolvedException $e)
		{
			$dependencies = array_map(function($dep) { return $dep['name'].' '.$dep['version']; }, $e->getDependencies());
			$m->add('error', __('orchestra::response.extensions.depends-on', array(
				'name'         => $name,
				'dependencies' => implode(', ', $dependencies)
			)));
		}

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}

	/**
	 * Deactivate an extension
	 *
	 * GET (:bundle)/extensions/deactivate/(:name)
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
	 * GET (:bundle)/extensions/configure/(:name)
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

		$extension_name = $memory->get("extensions.available.{$name}.name", $name);

		// Add basic form, allow extension to add custom configuration field to this
		$form = Form::of("orchestra.extension: {$name}", function ($form) use ($name, $config)
		{
			$form->row($config);

			$form->attr(array(
				'action' => handles("orchestra::extensions/configure/{$name}"),
				'method' => "POST",
			));

			$handles = Extension::option($name, 'handles');

			$form->fieldset(function ($fieldset) use ($handles, $name)
			{
				// We should only cater for custom URL handles for a route.
				if ( ! is_null($handles) and Extension::option($name, 'configurable') !== false)
				{
					$fieldset->control('input:text', 'handles', function ($control) use ($handles)
					{
						$control->label = 'Handle URL';
						$control->value = $handles;
					});
				}

				if ($name !== DEFAULT_BUNDLE)
				{
					$fieldset->control('select', 'web_upgrade', function ($control) use ($handles)
					{
						$control->value = function ($row)
						{
							return ($row->web_upgrade ? 'yes' : 'no');
						};

						$control->label = 'Upgrade via Web';
						$control->attr = array('role' => 'switcher');
						$control->options = array(
							'yes' => 'Yes',
							'no'  => 'No',
						);
					});
				}
			});
		});

		// Now lets the extension do their magic.
		Event::fire("orchestra.form: extension.{$name}", array($config, $form));

		$data = array(
			'eloquent'      => $config,
			'form'          => Form::of("orchestra.extension: {$name}"),
			'_title_'       => $extension_name,
			'_description_' => __("orchestra::title.extensions.configure")->get(),
		);

		return View::make('orchestra::resources.edit', $data);
	}

	/**
	 * Update extension configuration
	 *
	 * POST (:bundle)/extensions/configure/(:name)
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
		$loader = (array) $memory->get("extensions.active.{$name}", array());
		$m      = new Messages;

		// This part should be part of extension loader configuration. What saved here
		// wouldn't be part of extension configuration.
		if (isset($input['handles']) and ! empty($input['handles']))
		{
			$loader['handles'] = $input['handles'];
		}

		// Configure whether extension should be able to be upgraded via web.
		if (isset($input['web_upgrade']) and ! empty($input['web_upgrade']))
		{
			$input['web_upgrade']  = ('yes' === $input['web_upgrade'] ? true : false);
			$loader['web_upgrade'] = $input['web_upgrade'];
		}

		$memory->put("extensions.active.{$name}", $loader);

		// In any event where extension need to do some custom handling.
		Event::fire("orchestra.saving: extension.{$name}", array($config));

		$memory->put("extension_{$name}", $input);

		Event::fire("orchestra.saved: extension.{$name}", array($config));

		$m->add('success', __("orchestra::response.extensions.configure", compact('name')));

		return Redirect::to(handles('orchestra::extensions'))
			->with('message', $m->serialize());
	}

	/**
	 * Upgrade an extension
	 *
	 * GET (:bundle)/extensions/upgrade/(:name)
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_upgrade($name)
	{
		// we should only be able to upgrade extension which is already started
		if ( ! Extension::started($name) or $name === DEFAULT_BUNDLE) return Response::error('404');

		// we shouldn't upgrade extension which is not allowed to upgrade using web
		if (false === Extension::option($name, 'web_upgrade')) return Response::error('404');

		IoC::resolve('task: orchestra.upgrader', array(array($name)));

		Extension::publish($name);

		$m = Messages::make('success', __('orchestra::response.extensions.upgrade', compact('name')));

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}
}