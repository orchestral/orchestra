<?php

return array(
	'account' => array(
		'password' => array(
			'invalid' => 'Текущий пароль не сответствует тому, что записан у нас. Попробуйте ещё раз.',
			'update'  => 'Пароль был обновлён',
		),
		'profile' => array(
			'update' => 'Профиль был обновлён',
		),

	),

	'credential' => array(
		'invalid-combination' => 'Неверная комбинация логина и пароля',
		'logged-in'           => 'Вы успешно вошли',
		'logged-out'          => 'Вы успешно вышли',
		'unauthorized'        => 'You are not authorized to access this action',
		'register'            => array(
			'email-fail'    => 'Unable to send User Registration Confirmation E-mail',
			'email-send'    => 'User Registration Confirmation E-mail has been sent, please check your inbox',
			'existing-user' => 'This e-mail address is already associated with another user',
		),
	),

	'db-failed' => 'Невозможно сохранить в базу данных',
	'db-404'    => 'Ничего не найдено в базе данных',

	'extensions' => array(
		'activate'         => 'Расширение :name активировано',
		'deactivate'       => 'Расширение :name деактивировано',
		'configure'        => 'Настройки для расширения :name обновлены',
		'update'           => 'Extension :name has been updated',
		'depends-on'       => 'Extension :name was not activated because depends on :dependencies',
		'other-depends-on' => 'Extension :name was not deactivated because :dependencies depends on it',
	),

	'forgot' => array(
		'email-fail' => 'Невозможно отправить письмо с данными для восстановления',
		'email-send' => 'Данные для восстановления были высланы, проверьте входящие',
	),

	'settings' => array(
		'update' => 'Настройки приложения были обновлены',
	),

	'users' => array(
		'create' => 'Пользователь создан',
		'update' => 'Данные пользователя обновлены ',
		'delete' => 'Пользователь был удалён',
	),
);