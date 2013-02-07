<?php

/*
|--------------------------------------------------------------------------
| Use global bootstrap.phpunit.php
|--------------------------------------------------------------------------
*/

include_once dirname(__DIR__).'/bootstrap.phpunit.php';

/*
|--------------------------------------------------------------------------
| Define database configuration
|--------------------------------------------------------------------------
|
| Ensure that DB_ENV is based from setting provided in .travis.yml
|
*/
if (defined('DB_DRIVER'))
{
	switch (DB_DRIVER) 
	{
		case 'pdo/mysql' :
			Event::listen('orchestra.testable: setup-db', function ()
			{
				Config::set('database.connections.testdb', array(
					'driver'   => 'mysql',
					'host'     => 'localhost',
					'database' => 'orchestra',
					'username' => 'root',
					'password' => '',
					'charset'  => 'utf8',
					'prefix'   => '',
				));
			});

			Event::listen('orchestra.testable: teardown-db', function ()
			{
				return true;
			});
			break;
		case 'pdo/pgsql' :
			Event::listen('orchestra.testable: setup-db', function ()
			{
				Config::set('database.connections.testdb', array(
					'driver'   => 'pgsql',
					'host'     => 'localhost',
					'database' => 'orchestra',
					'username' => 'postgres',
					'password' => '',
					'charset'  => 'utf8',
					'prefix'   => '',
					'schema'   => 'public',
				));
			});

			Event::listen('orchestra.testable: teardown-db', function ()
			{
				return true;
			});
			break;
	}
}