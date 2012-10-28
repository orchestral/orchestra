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
	),

	'db-failed' => 'Невозможно сохранить в базу данных',
	'db-404'    => 'Ничего не найдено в базе данных',

	'extensions' => array(
		'activate'   => 'Расширение :name активировано',
		'deactivate' => 'Расширение :name деактивировано',
		'configure'  => 'Настройки для расширения :name обновлены',
		'upgrade'    => 'Extension :name has been upgraded',
	),

	'forgot' => array(
		'fail' => 'Невозможно отправить письмо с данными для восстановления',
		'send' => 'Данные для восстановления были высланы, проверьте входящие',
	),

	'settings' => array(
		'update' => 'Настройки приложения были обновлены',
		'upgrade' => 'Application has been upgraded',
	),

	'users' => array(
		'create' => 'Пользователь создан',
		'update' => 'Данные пользователя обновлены ',
		'delete' => 'Пользователь был удалён',
	),
);
