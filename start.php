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
| Orchestra IoC
|--------------------------------------------------------------------------
|
| Lets Orchestra run Laravel\CLI migration actions
 */
if( ! IoC::registered('task: orchestra.migrator'))
{
	IoC::register('task: orchestra.migrator', function($method, $bundle = null)
	{
		// Initiate the dependencies to Laravel\CLI migrate.
		$database = new Laravel\CLI\Tasks\Migrate\Database;
		$resolver = new Laravel\CLI\Tasks\Migrate\Resolver($database);
		$migrate  = new Laravel\CLI\Tasks\Migrate\Migrator($resolver, $database);

		if (method_exists($migrate, $method))
		{
			try
			{
				// We need to resolve to output buffering Task Migrator will echo some 
				// output to terminal.
				ob_start();

				$migrate->{$method}($bundle);

				ob_end_clean();
			}
			catch (Exception $e) {}
		}
		else 
		{
			throw new Exception('Unable to find migration action');
		}
		
	});
}

if( ! IoC::registered('task: orchestra.publisher'))
{
	IoC::register('task: orchestra.publisher', function($bundle = null)
	{
		// Initiate the dependencies to Laravel\CLI bundle publisher.
		$publisher = new Laravel\CLI\Tasks\Bundle\Publisher;

		try
		{
			// We need to resolve to output buffering Task Migrator will echo some 
			// output to terminal.
			ob_start();

			$publisher->publish($bundle);

			ob_end_clean();
		}
		catch (Exception $e) {}
	});	
}

if( ! IoC::registered('orchestra.mailer'))
{
	IoC::register('orchestra.mailer', function()
	{
		$memory = Orchestra\Core::memory();
		$config = $memory->get('email');
		$driver = $config['default'];

		return Messages::factory($driver, $config["transports.{$driver}"]);
	});
}

/*
|--------------------------------------------------------------------------
| Orchestra Helpers
|--------------------------------------------------------------------------
 */
include_once Bundle::path('orchestra').'helpers.php';