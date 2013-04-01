<?php

$orchestra = Bundle::path('orchestra');

/*
|--------------------------------------------------------------------------
| Orchestra Library
|--------------------------------------------------------------------------
|
| Map Orchestra Library using PSR-0 standard namespace.
|
*/

Autoloader::namespaces(array(
	'Orchestra\Model'     => $orchestra.'models/',
	'Orchestra\Presenter' => $orchestra.'presenters/',
	'Orchestra\Support'   => $orchestra.'supports/',
	'Orchestra\Testable'  => $orchestra.'tests/testable/',
	'Orchestra'           => $orchestra.'libraries/',
));

Autoloader::map(array(
	// Facade for Orchestra\Core
	'Orchestra' => $orchestra.'orchestra.php',

	// Exceptions
	'Orchestra\Extension\UnresolvedException'
		=> $orchestra.'libraries/extension/exceptions.php',
	'Orchestra\Extension\FilePermissionException'
		=> $orchestra.'libraries/extension/exceptions.php',
	'Orchestra\Support\FTP\RuntimeException'
		=> $orchestra.'supports/ftp/exceptions.php',
	'Orchestra\Support\FTP\ServerException'
		=> $orchestra.'supports/ftp/exceptions.php',
));

/*
|--------------------------------------------------------------------------
| Map Orchestra\Support Classes as Orchestra
|--------------------------------------------------------------------------
|
| This would allow user to access Orchestra namespace without having to
| know Orchestra\Support.
|
*/

Autoloader::alias('Orchestra\Support\Acl', 'Orchestra\Acl');
Autoloader::alias('Orchestra\Support\Auth', 'Orchestra\Auth');
Autoloader::alias('Orchestra\Support\Fluent', 'Orchestra\Fluent');
Autoloader::alias('Orchestra\Support\Form', 'Orchestra\Form');
Autoloader::alias('Orchestra\Support\HTML', 'Orchestra\HTML');
Autoloader::alias('Orchestra\Support\Memory', 'Orchestra\Memory');
Autoloader::alias('Orchestra\Support\Messages', 'Orchestra\Messages');
Autoloader::alias('Orchestra\Support\Site', 'Orchestra\Site');
Autoloader::alias('Orchestra\Support\Decorator', 'Orchestra\Decorator');
Autoloader::alias('Orchestra\Support\Table', 'Orchestra\Table');

/*
|--------------------------------------------------------------------------
| Set default path for extension.
|--------------------------------------------------------------------------
|
| Allow path('orchestra.extension') default path to be customizable.
|
*/

set_path('orchestra.extension', path('bundle'));

/*
|--------------------------------------------------------------------------
| Orchestra Dependencies
|--------------------------------------------------------------------------
|
| Add Orchestra helpers function and dependencies.
|
*/

include_once $orchestra.'helpers.php';
include_once $orchestra.'includes/dependencies.php';
include_once $orchestra.'includes/events.php';
include_once $orchestra.'includes/macros.php';

/*
|--------------------------------------------------------------------------
| Start Your Engine
|--------------------------------------------------------------------------
*/

Orchestra\Core::start();
