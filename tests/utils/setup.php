<?php

Bundle::start('orchestra');

if ( ! Orchestra\Installer::installed())
{
	Orchestra\Installer\Runner::install();
	Request::foundation()->server->add(array(
		'REQUEST_METHOD' => 'POST',
		'email'          => 'example@test.com',
		'password'       => '123456',
		'fullname'       => 'Orchestra TestRunner',
	));

	Orchestra\Installer\Runner::create_user();	
}