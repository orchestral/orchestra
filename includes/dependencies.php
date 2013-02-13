<?php

/*
|--------------------------------------------------------------------------
| Load dependencies
|--------------------------------------------------------------------------
|
| Register and start Hybrid bundle if it's not registered in
| `application/bundles.php`.
|
*/

 ! Bundle::exists('hybrid') and Bundle::register('hybrid');

Bundle::start('hybrid');

/*
|--------------------------------------------------------------------------
| Map Hybrid Classes as Orchestra
|--------------------------------------------------------------------------
|
| This would allow user to access Orchestra namespace without having to
| know Hybrid.
|
*/

Autoloader::alias('Hybrid\Acl', 'Orchestra\Acl');
Autoloader::alias('Hybrid\Form', 'Orchestra\Form');
Autoloader::alias('Hybrid\HTML', 'Orchestra\HTML');
Autoloader::alias('Hybrid\Memory', 'Orchestra\Memory');
Autoloader::alias('Hybrid\Response', 'Orchestra\Response');
Autoloader::alias('Hybrid\Table', 'Orchestra\Table');

/*
|--------------------------------------------------------------------------
| Orchestra IoC (Migration)
|--------------------------------------------------------------------------
|
| Lets Orchestra run Laravel\CLI migration actions.
|
*/

if ( ! IoC::registered('task: orchestra.migrator'))
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
				// We need to resolve to output buffering Task Migrator will
				// echo some output to terminal.
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

/*
|--------------------------------------------------------------------------
| Orchestra IoC (Publisher)
|--------------------------------------------------------------------------
|
| Lets Orchestra run Laravel\CLI bundle asset publish actions. This is an
| alias to `php artisan bundle:publish`.
|
*/

if ( ! IoC::registered('task: orchestra.publisher'))
{
	IoC::register('task: orchestra.publisher', function($bundle = null)
	{
		// If bundle doesn't have a public folder, avoid all file 
		// permission fuss.
		if ( ! is_dir(Bundle::path($bundle).'public'.DS)) return ;

		// Initiate the dependencies to Laravel\CLI bundle publisher.
		$publisher = new Laravel\CLI\Tasks\Bundle\Publisher;
		$directory = path('public').'bundles'.DS;

		if (is_dir($directory.$bundle.DS)) $directory = $directory.$bundle.DS;

		@chmod($directory, 0777);

		if ( ! is_writable($directory)) 
		{
			throw new Orchestra\Extension\FilePermissionException(
				"Unable to write on directory [{$directory}] due to permission issue."
			);
		}

		try
		{
			// We need to resolve to output buffering Task Migrator will echo
			// some output to terminal.
			ob_start();

			$publisher->publish($bundle);

			ob_end_clean();
		}
		catch (Exception $e) {}

		@chmod($directory, 0755);
	});
}

/*
|--------------------------------------------------------------------------
| Orchestra IoC (Upgrader)
|--------------------------------------------------------------------------
|
| Lets Orchestra run Laravel\CLI bundle upgrade actions. This is an alias to
| `php artisan bundle:upgrade`.
|
*/

if ( ! IoC::registered('task: orchestra.upgrader'))
{
	if ( ! IoC::registered('bundle.provider: github'))
	{
		IoC::singleton('bundle.provider: github', function()
		{
			return new Laravel\CLI\Tasks\Bundle\Providers\Github;
		});
	}

	IoC::singleton('task: orchestra.upgrader', function($bundle)
	{
		$repository = new Laravel\CLI\Tasks\Bundle\Repository;
		$upgrader   = new Laravel\CLI\Tasks\Bundle\Bundler($repository);
		$directory = path('bundle');

		@chmod($directory, 0777);

		if ( ! is_writable($directory)) 
		{
			throw new Orchestra\Extension\FilePermissionException(
				"Unable to write on directory [{$directory}] due to permission issue."
			);
		}

		try
		{
			// We need to resolve to output buffering Task Upgrader will echo
			// some output to terminal.
			ob_start();

			$upgrader->upgrade($bundle);

			ob_end_clean();
		}
		catch (Exception $e) {}

		@chmod($directory, 0755);
	});
}

/*
|--------------------------------------------------------------------------
| Orchestra Mailer IoC
|--------------------------------------------------------------------------
|
| Lets Orchestra handle mailer (integration with Message bundle) using IoC.
|
*/

if ( ! IoC::registered('orchestra.mailer'))
{
	IoC::register('orchestra.mailer', function($from = true)
	{
		// Ensure Messages bundle is registered
		if ( ! Bundle::exists('messages')) Bundle::register('messages');

		// Ensure it's started as well
		if ( ! Bundle::started('messages')) Bundle::start('messages');

		$memory     = Orchestra\Core::memory();

		$config     = $memory->get('email');
		$driver     = $config['default'];
		$transports = $config['transports'];
		$email      = $config['from'];

		Config::set('messages::config.transports', $transports);

		$mailer = Message::instance($driver);

		if ($from === true)
		{
			$mailer->from($email, $memory->get('site.name', 'Orchestra'));
		}

		return $mailer;
	});
}

/*
|--------------------------------------------------------------------------
| Orchestra Memory IoC
|--------------------------------------------------------------------------
|
| Lets Orchestra handle Orchestra\Memory instance using IoC.
|
*/

if ( ! IoC::registered('orchestra.memory'))
{
	IoC::singleton('orchestra.memory', function ()
	{
		return Orchestra\Memory::make('fluent.orchestra_options');
	});
}

Orchestra\Memory::extend('user', function()
{
	return new Orchestra\Repository\User;
});

/*
|--------------------------------------------------------------------------
| Orchestra View IoC for Hybrid Bundle
|--------------------------------------------------------------------------
|
| Lets Orchestra handle View instance using IoC for Hybrid Bundle.
|
*/

IoC::register('hybrid.view', function ($view)
{
	return Orchestra\View::make($view);
});