<?php

/*
|--------------------------------------------------------------------------
| Orchestra Library
|--------------------------------------------------------------------------
|
| Map Orchestra Library using PSR-0 standard namespace.
|
*/

Autoloader::namespaces(array(
	'Orchestra\Model'     => Bundle::path('orchestra').'models'.DS,
	'Orchestra\Presenter' => Bundle::path('orchestra').'presenters'.DS,
	'Orchestra'           => Bundle::path('orchestra').'libraries'.DS,
));

Autoloader::map(array(
	// Facade for Orchestra\Core
	'Orchestra' => Bundle::path('orchestra').'orchestra'.EXT,

	// Exceptions
	'Orchestra\Extension\UnresolvedException'
		=> Bundle::path('orchestra').'libraries'.DS.'extension'.DS.'exceptions'.EXT,
	'Orchestra\Extension\FilePermissionException'
		=> Bundle::path('orchestra').'libraries'.DS.'extension'.DS.'exceptions'.EXT,
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

include_once Bundle::path('orchestra').'helpers'.EXT;
include_once Bundle::path('orchestra').'includes'.DS.'dependencies'.EXT;
include_once Bundle::path('orchestra').'includes'.DS.'events'.EXT;
include_once Bundle::path('orchestra').'includes'.DS.'macros'.EXT;

/*
|--------------------------------------------------------------------------
| Start Your Engine
|--------------------------------------------------------------------------
*/

Orchestra\Core::start();
