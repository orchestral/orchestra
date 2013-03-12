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

			// attach Model and set pagination option to true
			$table->with($model, true);

			// Add columns
			$table->column('fullname', function ($column)
			{
				$column->label(__('orchestra::label.users.fullname'));
				$column->escape(false);
				$column->value(function ($row)
				{
					$roles = $row->roles;
					$value = array();

					foreach ($roles as $role)
					{
						$value[] = HTML::create('span', e($role->name), array(
							'class' => 'label label-info',
							'role'  => 'role',
						));
					}

					return implode('', array(
						HTML::create('strong', e($row->fullname)),
						HTML::create('br'),
						HTML::create('span', HTML::raw(implode(' ', $value)), array(
							'class' => 'meta',
						)),
					));
				});
			});

			$table->column('email', function ($column)
			{
				$column->label(__('orchestra::label.users.email'));
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
				$column->label('');
				$column->escape(false);
				$column->label_attributes(array('class' => 'th-action'));
				$column->value(function ($row)
				{
					$btn = array();
					$btn[] = HTML::link(
						handles("orchestra::users/view/{$row->id}"),
						__('orchestra::label.edit'),
						array(
							'class' => 'btn btn-mini btn-warning',
							'role'  => 'edit',
						)
					);

					if (Auth::user()->id !== $row->id)
					{
						$btn[] = HTML::link(
							handles("orchestra::users/delete/{$row->id}"),
							__('orchestra::label.delete'),
							array(
								'class' => 'btn btn-mini btn-danger',
								'role'  => 'delete',
							)
						);
					}

					return HTML::create(
						'div',
						HTML::raw(implode('', $btn)),
						array('class' => 'btn-group')
					);
				});
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
			$form->attributes(array(
				'action' => handles("orchestra::users/view/{$model->id}"),
				'method' => 'POST',
			));

			$form->hidden('id');

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'email', function ($control)
				{
					$control->label(__('orchestra::label.users.email'));
				});

				$fieldset->control('input:text', 'fullname', function($control)
				{
					$control->label(__('orchestra::label.users.fullname'));
				});

				$fieldset->control('input:password', 'password', function($control)
				{
					$control->label(__('orchestra::label.users.password'));
				});

				$fieldset->control('select', 'roles[]', function ($control)
				{
					$control->label(__('orchestra::label.users.roles'));
					$control->options(Role::lists('name', 'id'));
					$control->attributes(array('multiple' => true));
					$control->value(function ($row)
					{
						// get all the user roles from objects
						$roles = array();
						foreach ($row->roles as $row) $roles[] = $row->id;
						return $roles;
					});
				});
			});
		});
	}
}