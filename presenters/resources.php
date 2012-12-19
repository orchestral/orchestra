<?php namespace Orchestra\Presenter;

use Orchestra\HTML, 
	Orchestra\Table;

class Resources {
	
	/**
	 * Table View Generator for Orchestra\Resources.
	 *
	 * @static
	 * @access public
	 * @param  Orchestra\Resources  $model
	 * @return Orchestra\Table
	 */
	public static function table($model)
	{
		return Table::of('orchestra.resources: list', function ($table) use ($model)
		{
			$table->empty_message = __('orchestra::label.no-data');

			// Add HTML attributes option for the table.
			$table->attr('class', 'table table-bordered table-striped');

			// attach the list
			$table->rows($model);

			$table->column('name', function ($column)
			{
				$column->value = function ($row)
				{
					$link = HTML::link(handles("orchestra::resources/{$row->id}"), $row->name);
					return HTML::create('strong', HTML::raw($link));
				};
			});
		});
	}
}