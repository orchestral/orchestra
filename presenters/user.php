<?php namespace Orchestra\Presenter;

use \Auth, 
	Orchestra\Form, 
	Orchestra\HTML, 
	Orchestra\Table,
	Orchestra\Model\Role;

class User {

	/**
	 * Table View Generator for Orchestra\Model\User.
	 *
	 * @static
	 * @access public
	 * @param  Orchestra\Model\User $model
	 * @return Orchestra\Table
	 */
	public static function table($model)
	{
		return Table::of('orchestra.users', function ($table) use ($model)
		{
			$table->empty_message = __('orchestra::label.no-data');

			// Add HTML attributes option for the table.
			$table->attr('class', 'table table-bordered table-striped');

			// attach Model and set pagination option to true
			$table->with($model, true);

			// Add columns
			$table->column('id');

			$table->column('fullname', function ($column)
			{
				$column->label = __('orchestra::label.users.fullname');
				$column->value = function ($row)
				{
					$roles = $row->roles;
					$value = array();

					foreach ($roles as $role)
					{
						$value[] = HTML::create('span', $role->name, array(
							'class' => 'label label-info',
						));
					}

					return implode('', array(
						HTML::create('strong', $row->fullname),
						HTML::create('br'),
						HTML::create('span', HTML::raw(implode(' ', $value)), array(
							'class' => 'meta',
						)),
					));
				};

			});

			$table->column('email', function ($column)
			{
				$column->label = __('orchestra::label.users.email');
				$column->value = function ($row)
				{
					return $row->email;
				};
			});
		});
	}

	/**
	 * Table actions View Generator for Orchestra\Model\User.
	 *
	 * @static
	 * @access public
	 * @param  Orchestra\Table  $model
	 * @return Orchestra\Table
	 */
	public static function table_actions(Table $table)
	{
		$table->extend(function ($table)
		{
			$table->column('action', function ($column)
			{
				$column->label      = '';
				$column->label_attr = array('class' => 'th-action');
				$column->value      = function ($row)
				{
					$btn = array();
					$btn[] = HTML::link(
						handles('orchestra::users/view/'.$row->id),
						__('orchestra::label.edit'),
						array('class' => 'btn btn-mini')
					);

					if (Auth::user()->id !== $row->id)
					{
						$btn[] = HTML::link(
							handles('orchestra::users/delete/'.$row->id),
							__('orchestra::label.delete'),
							array('class' => 'btn btn-mini btn-danger')
						);
					}

					return HTML::create(
						'div',
						HTML::raw(implode('', $btn)),
						array('class' => 'btn-group')
					);
				};
			});
		});
	}

	/**
	 * Form View Generator for Orchestra\Model\User.
	 *
	 * @static
	 * @access public
	 * @param  Orchestra\Model\User $model
	 * @return Orchestra\Form
	 */
	public static function form($model)
	{
		return Form::of('orchestra.users', function ($form) use ($model)
		{
			$form->row($model);
			$form->attr(array(
				'action' => handles("orchestra::users/view/{$model->id}"),
				'method' => 'POST',
			));

			$form->hidden('id');

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'email', function ($control)
				{
					$control->label =  __('orchestra::label.users.email');
				});

				$fieldset->control('input:text', 'fullname', function($control)
				{
					$control->label = __('orchestra::label.users.fullname');
				});

				$fieldset->control('input:password', 'password', function($control)
				{
					$control->label = __('orchestra::label.users.password');
				});

				$fieldset->control('select', 'roles[]', function ($control)
				{
					$options          = Role::lists('name', 'id');
					$control->label   = __('orchestra::label.users.roles');
					$control->name    = 'roles[]';
					$control->options = $options;
					$control->attr    = array('multiple' => true);
					$control->value   = function ($row) use ($options)
					{
						// get all the user roles from objects
						$roles = array();

						foreach ($row->roles as $row) $roles[] = $row->id;

						return $roles;
					};
				});
			});
		});
	}
}