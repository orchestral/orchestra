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
			$form->attr(array(
				'action' => $action,
				'method' => 'POST',
			));

			$form->hidden('id');

			$form->fieldset(function ($fieldset)
			{
				$fieldset->control('input:text', 'email', function ($control)
				{
					$control->label = __('orchestra::label.users.email');
				});

				$fieldset->control('input:text', 'fullname', function ($control)
				{
					$control->label = __('orchestra::label.users.fullname');
				});
			});
		});
	}
}