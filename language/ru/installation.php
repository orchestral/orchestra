<?php

return array(
	'hide-password' => 'Database password is hidden for security.',
	'verify'        => 'Please ensure following configuration is correct based on your :filename.',
	'solution'      => 'Solution',

	'status'     => array(
		'still' => 'Still Workable',
		'work'  => 'Workable',
		'not'   => 'Not Workable',
	),
	'connection' => array(
		'status'  => 'Connection Status',
		'success' => 'Successful',
		'fail'    => 'Failed',
	),
	
	'auth'     => array(
		'title'       => 'Authentication Setting',
		'driver'      => 'Driver',
		'model'       => 'Model',
		'requirement' => array(
			'driver'     => 'Orchestra only work with Eloquent Driver for Auth',
			'instanceof' => 'Model name should be an instance of :class',
		),
	),
	'database' => array(
		'title'    => 'Database Setting',
		'host'     => 'Host',
		'name'     => 'Database Name',
		'password' => 'Password',
		'username' => 'Username',
		'type'     => 'Database Type',
	),
	'system'   => array(
		'title'       => 'System Requirement',
		'description' => 'Please ensure the following requirement is profilled before installing Orchestra Platform.',
		'requirement' => 'Requirement',
		'status'      => 'Status',

		'storage_writable' => array(
			'name' => "Writable to :path",
			'solution' => "Change the file permission to 0777, however it might cause a security write if this folder is accessible from the web.",
		),
		'bundle_writable' => array(
			'name' => "Writable to :path",
			'solution' => "Change the file permission to 0777, however it might cause a security write if this folder is accessible from the web.",
		),
		'asset_writable' => array(
			'name'     => "Writable to :path",
			'solution' => "Delete the folder, Orchestra Platform will generate a new one with the right set of file ownership.",
		),
	),
);