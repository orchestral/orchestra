<?php namespace Orchestra\Presenter;

use Orchestra\Form,
	Orchestra\HTML;

class Setting {
	
	/**
	 * Form View Generator for Setting Page.
	 *
	 * @static
	 * @access public			
	 * @param  Laravel\Fluent   $model
	 * @return Orchestra\Form
	 */
	public static function form($model)
	{
		return Form::of('orchestra.settings', function ($form) use ($model)
		{
			$form->row($model);
			$form->attr(array(
				'action' => handles('orchestra::settings'),
				'method' => 'POST',
			));

			$form->fieldset('Application', function ($fieldset)
			{
				$fieldset->control('input:text', 'site_name', function ($control)
				{
					$control->label = __('orchestra::label.name');
				});

				$fieldset->control('textarea', 'site_description', function ($control)
				{
					$control->label = __('orchestra::label.description');
					$control->attr  = array('rows' => 3);
				});

				$fieldset->control('select', 'site_user_registration', function ($control)
				{
					$control->label   = __('orchestra::label.settings.user-registration');
					$control->attr    = array(
						'role' => 'switcher',
					);
					$control->options = array(
						'yes' => 'Yes',
						'no'  => 'No',
					);
				});
			});

			$form->fieldset('E-mail and Messaging', function ($fieldset) use ($model)
			{
				$fieldset->control('select', 'email_default', function ($control)
				{
					$control->label   = __('orchestra::label.email.transport');
					$control->options = array(
						'mail'     => 'Mail',
						'smtp'     => 'SMTP',
						'sendmail' => 'Sendmail',
					);
				});

				$fieldset->control('input:text', __('orchestra::label.email.host')->get(), 'email_smtp_host');
				$fieldset->control('input:text', __('orchestra::label.email.port')->get(), 'email_smtp_port');
				$fieldset->control('input:text', __('orchestra::label.email.username')->get(), 'email_smtp_username');
				$fieldset->control('input:password', 'email_smtp_password', function ($control) use ($model)
				{
					$help = array(
						HTML::create('span', str_repeat('*', strlen($model->email_smtp_password))),
						'&nbsp;&nbsp;',
						HTML::link('#', __('orchestra::label.email.change_password')->get(), array(
							'id' => 'smtp_change_password_button',
							'class' => 'btn btn-mini btn-warning',
						)),
						\Laravel\Form::hidden('stmp_change_password', 'no'),
					);

					$control->label = __('orchestra::label.email.password');
					$control->help  = HTML::create('span', HTML::raw(implode('', $help)), array(
						'id' => 'smtp_change_password_container',
					));
				});
				$fieldset->control('input:text', __('orchestra::label.email.encryption')->get(), 'email_smtp_encryption');
				$fieldset->control('input:text', __('orchestra::label.email.command')->get(), 'email_sendmail_command');
			});
		});
	}
}