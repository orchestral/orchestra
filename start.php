<?php

/*
|--------------------------------------------------------------------------
| Orchestra Library
|--------------------------------------------------------------------------
|
| Map Orchestra Library using PSR-0 standard namespace. 
 */
Autoloader::namespaces(array(
	'Orchestra\Model' => Bundle::path('orchestra').'models'.DS,
	'Orchestra'       => Bundle::path('orchestra').'libraries'.DS,
));

/*
|--------------------------------------------------------------------------
| Load dependencies
|--------------------------------------------------------------------------
|
| Register and start Hybrid bundle if it's not registered in 
| application/bundles.php
 */
if ( ! Bundle::exists('hybrid'))
{
	Bundle::register('hybrid');
	Bundle::start('hybrid');
}

/*
|--------------------------------------------------------------------------
| Orchestra Events Listener
|--------------------------------------------------------------------------
|
| Lets listen to when Orchestra bundle is started.
 */ 
Event::listen('laravel.started: orchestra', function () 
{
	Orchestra\Core::start();
});

/*
|--------------------------------------------------------------------------
| Orchestra Helpers
|--------------------------------------------------------------------------
 */
include_once Bundle::path('orchestra').'helpers.php';