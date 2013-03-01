<?php namespace Orchestra\Presenter;

use Orchestra\Form;

class Account {
	
	/**
	 * Form view generator for User Account.
	 *
	 * @static
	 * @access public
	 * @param  Orchestra\Model\User $model
	 * @param  string               $action
	 * @return Orchestra\Form
	 */
	public static function form($model, $action)
	{
		return Form::of('orchestra.account', function ($form) use ($model, $action)
		{
			$form->row($model);
			$form->attributes(array(
				'action' => $action,
				'method' => 'POST',
			));

			$form->hidden('id');

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'email', function ($control)
				{
					$control->label(__('orchestra::label.users.email'));
				});

				$fieldset->control('input:text', 'fullname', function ($control)
				{
					$control->label(__('orchestra::label.users.fullname'));
				});
			});
		});
	}

	/**
	 * Form view generator for user account edit password.
	 * 
	 * @access public
	 * @param  Orchestra\Model\User $model
	 * @return Orchestra\Form
	 */
	public static function form_password($model)
	{
		return Form::of('orchestra.account: password', function ($form) use ($model)
		{
			$form->row($model);
			$form->attributes(array(
				'action' => handles('orchestra::account/password'),
				'method' => 'POST',
			));

			$form->hidden('id');

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:password', 'current_password', function ($control)
				{
					$control->label(__('orchestra::label.account.current_password'));
				});

				$fieldset->control('input:password', 'new_password', function ($control)
				{
					$control->label(__('orchestra::label.account.new_password'));
				});

				$fieldset->control('input:password', 'confirm_password', function ($control)
				{
					$control->label(__('orchestra::label.account.confirm_password'));
				});
			});
		});
	}
}