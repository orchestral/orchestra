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

			$form->attr(array(
				'action' => handles("orchestra::extensions/configure/{$name}"),
				'method' => "POST",
			));

			$handles = E::option($name, 'handles');

			$form->fieldset(function ($fieldset) use ($handles, $name)
			{
				// We should only cater for custom URL handles for a route.
				if ( ! is_null($handles) and E::option($name, 'configurable') !== false)
				{
					$fieldset->control('input:text', 'handles', function ($control) use ($handles)
					{
						$control->label('Handle URL');
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