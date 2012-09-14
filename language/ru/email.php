<?php

return array(
	'forgot' => array(
		'subject' => "[:site] восстановил пароль",
		'message' => "Здравствуйте :fullname,
Мы получили запрос на восстановление пароля. Если это было ошибкой, просто проигнорируйте это письмо и ничего не произойдёт.

Чтобы восстановить пароль, пройдите по ссылке :url и сбросьте пароль.
",
	),
	'reset' => array(
		'subject' => "[:site] Ваш новый пароль",
		'message' => "Был изменён пароль для :fullname

Пожалуйста, войдите под временным паролем: :password 

Когда войдёте, пожалуйста, измените свой пароль.",
	)
);