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
	'Orchestra\Model'     => $orchestra.'models'.DS,
	'Orchestra\Presenter' => $orchestra.'presenters'.DS,
	'Orchestra\Support'   => $orchestra.'supports'.DS,
	'Orchestra'           => $orchestra.'libraries'.DS,
));

Autoloader::map(array(
	// Facade for Orchestra\Core
	'Orchestra' => $orchestra.'orchestra'.EXT,

	// Exceptions
	'Orchestra\Extension\UnresolvedException'
		=> $orchestra.'libraries'.DS.'extension'.DS.'exceptions'.EXT,
	'Orchestra\Extension\FilePermissionException'
		=> $orchestra.'libraries'.DS.'extension'.DS.'exceptions'.EXT,
));

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

include_once $orchestra.'helpers'.EXT;
include_once $orchestra.'includes'.DS.'dependencies'.EXT;
include_once $orchestra.'includes'.DS.'events'.EXT;
include_once $orchestra.'includes'.DS.'macros'.EXT;

/*
|--------------------------------------------------------------------------
| Start Your Engine
|--------------------------------------------------------------------------
*/

Orchestra\Core::start();
