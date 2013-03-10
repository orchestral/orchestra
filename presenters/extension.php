<?php namespace Orchestra\Presenter;

use Orchestra\Extension as E,
	Orchestra\Form,
	Orchestra\HTML;

class Extension {

	/**
	 * Form View Generator for Orchestra\Extension.
	 *
	 * @static
	 * @access public
	 * @param  string               $name
	 * @param  Orchestra\Extension  $config
	 * @return Orchestra\Form
	 */
	public static function form($name, $config)
	{
		return Form::of("orchestra.extension: {$name}", function ($form) use ($name, $config)
		{
			$form->row($config);

			$form->attributes(array(
				'action' => handles("orchestra::extensions/configure/{$name}"),
				'method' => "POST",
			));

			$handles      = isset($config->handles) ? $config->handles : E::option($name, 'handles');
			$configurable = isset($config->configurable) ? $config->configurable : true;

			$form->fieldset(function ($fieldset) use ($handles, $name, $configurable)
			{
				// We should only cater for custom URL handles for a route.
				if ( ! is_null($handles) and $configurable !== false)
				{
					$fieldset->control('input:text', 'handles', function ($control) use ($handles)
					{
						$control->label(__('orchestra::label.extensions.handles'));
						$control->value($handles);
					});
				}

				$fieldset->control('input:text', 'migrate', function ($control) use ($handles, $name)
				{
					$control->label(__('orchestra::label.extensions.update'));

					$control->field(function() use ($name)
					{
						return HTML::link(
							handles("orchestra::extensions/update/{$name}"),
							__('orchestra::label.extensions.actions.update'),
							array('class' => 'btn btn-info')
						);
					});
				});
			});
		});
	}
}