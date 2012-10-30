<?php

include_once "controller_testcase.php";

if ( ! Orchestra\Installer::installed())
{
	Orchestra\Installer\Runner::install();

	Request::$foundation = Symfony\Component\HttpFoundation\LaravelRequest::createFromGlobals();

	Request::foundation()->server->add(array(
		'REQUEST_METHOD' => 'POST',
	));

	Orchestra\Installer\Runner::create_user(array(
		'email'    => 'example@test.com',
		'password' => '123456',
		'fullname' => 'Orchestra TestRunner',
	));	

	// Lets restart Orchestra
	Orchestra\Core::done();
	Orchestra\Core::start();
}