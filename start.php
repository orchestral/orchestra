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

Autoloader::map(array(
	'Orchestra' => Bundle::path('orchestra').'orchestra'.EXT,
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
}

Bundle::start('hybrid');

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

Event::listen('laravel.done', function () 
{
	Orchestra\Core::done();
});

Event::listen('orchestra.started: backend', function ()
{
	Orchestra\View::$theme = 'backend';
});

/*
|--------------------------------------------------------------------------
| Orchestra Dependencies
|--------------------------------------------------------------------------
|
| Add Orchestra helpers function and dependencies
*/

include_once Bundle::path('orchestra').'helpers'.EXT;
include_once Bundle::path('orchestra').'includes'.DS.'dependencies'.EXT;