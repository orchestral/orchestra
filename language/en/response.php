<?php

return array(
	'account' => array(
		'password' => array(
			'invalid' => 'Current password does not match our record, please try again',
			'update'  => 'Your password has been updated',
		),
		'profile' => array(
			'update' => 'Your profile has been updated',
		),

	),

	'credential' => array(
		'invalid-combination' => 'Invalid user and password combination',
		'logged-in'           => 'You has been logged in',
		'logged-out'          => 'You have been logged out',
		'unauthorized'        => 'You are not authorized to access this action',
	),

	'db-failed' => 'Unable to save to database',
	'db-404'    => 'Requested data is not available on the database',

	'extensions' => array(
		'activate'   => 'Extension :name activated',
		'deactivate' => 'Extension :name deactivate',
		'configure'  => 'Configuration for Extension :name has been updated',
		'upgrade'    => 'Extension :name has been upgraded',
	),

	'forgot' => array(
		'fail' => 'Unable to send reset password email',
		'send' => 'Reset password email has been sent, please check your inbox',
	),

	'settings' => array(
		'update'  => 'Application settings has been updated',
		'upgrade' => 'Application has been upgraded',
	),

	'users' => array(
		'create' => 'User has been created',
		'update' => 'User has been updated',
		'delete' => 'User has been deleted',
	),
);