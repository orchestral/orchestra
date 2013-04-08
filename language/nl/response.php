<?php

return array(
	'account' => array(
		'password' => array(
			'invalid' => 'Het huidge wachtwoord is niet hetzelfde als in de database, probeer het alstublieft nog een keer.',
			'update'  => 'Uw wachtwoord is veranderd',
		),
		'profile' => array(
			'update' => 'Uw profiel is bijgewerkt.',
		),

	),

	'credential' => array(
		'invalid-combination' => 'Incorrecte combinatie van wachtwoord en gebruikersnaam',
		'logged-in'           => 'U bent ingelogd',
		'logged-out'          => 'U bent uitgelogd',
		'unauthorized'        => 'U bent niet geautoriseerd om deze actie uit te voeren.',
		'register'            => array(
			'email-fail'    => 'We konden niet de e-mail sturen waarin staat dat de registratie successvol was',
			'email-send'    => 'User Registration Confirmation E-mail has been sent, please check your inbox',
			'existing-user' => 'This e-mail address is already associated with another user',
		),
	),

	'db-failed' => 'Unable to save to database',
	'db-404'    => 'Requested data is not available on the database',

	'extensions' => array(
		'activate'         => 'Extension :name activated',
		'deactivate'       => 'Extension :name deactivate',
		'configure'        => 'Configuration for Extension :name has been updated',
		'update'           => 'Extension :name has been updated',
		'depends-on'       => 'Extension :name was not activated because depends on :dependencies',
		'other-depends-on' => 'Extension :name was not deactivated because :dependencies depends on it',
	),

	'forgot' => array(
		'email-fail' => 'Unable to send Reset Password E-mail',
		'email-send' => 'Reset Password E-mail has been sent, please check your inbox',
	),

	'settings' => array(
		'update'  => 'Application settings has been updated',
	),

	'users' => array(
		'create' => 'User has been created',
		'update' => 'User has been updated',
		'delete' => 'User has been deleted',
	),
);
