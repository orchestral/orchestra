<?php

/*
|--------------------------------------------------------------------------
| Installer
|--------------------------------------------------------------------------
|
| Run installation route when Orchestra is not installed yet.
|
*/

Route::any('(:bundle)/installer/?(:any)?/?(:num)?', function ($action = 'index', $steps = 0)
{
	// we should disable this routing when the system detect it's already
	// running/installed.
	if (Orchestra\Installer::installed()
		and ( ! ($action === 'steps' && intval($steps) === 2)))
	{
		return Response::error('404');
	}

	// Otherwise, install it right away.
	return Controller::call("orchestra::installer@{$action}", array($steps));
});

/*
|--------------------------------------------------------------------------
| Default Routing
|--------------------------------------------------------------------------
*/

Route::any('(:bundle)', array(
	'before' => 'orchestra::installed|orchestra::auth',
	function ()
	{
		// Display the dashboard
		return Controller::call('orchestra::dashboard@index');
	},
));

/*
|--------------------------------------------------------------------------
| Credential Routing
|--------------------------------------------------------------------------
*/

Route::any('(:bundle)/(login|register|logout)', function ($action)
{
	return Controller::call("orchestra::credential@{$action}");
});

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
|
| Detects all controller under Orchestra bundle and register it to routing.
|
*/

Route::controller(array(
	'orchestra::account',
	'orchestra::bundles',
	'orchestra::credential',
	'orchestra::dashboard',
	'orchestra::extensions',
	'orchestra::forgot',
	'orchestra::manages',
	'orchestra::pages',
	'orchestra::publisher',
	'orchestra::resources',
	'orchestra::settings',
	'orchestra::users',
));

/*
|--------------------------------------------------------------------------
| Route Filtering
|--------------------------------------------------------------------------
*/

Route::filter('orchestra::auth', function ()
{
	Session::flash('orchestra.redirect', Input::get('redirect'));

	// Redirect the user to login page if user is not logged in.
	if (Auth::guest()) return Redirect::to(handles('orchestra::login'));
});

Route::filter('orchestra::not-auth', function ()
{
	Session::flash('orchestra.redirect', Input::get('redirect'));

	// Redirect the user to login page if user is not logged in.
	if ( ! Auth::guest()) return Redirect::to(handles('orchestra'));
});

Route::filter('orchestra::manage-users', function ()
{
	// Redirect the user to login page if user is not logged in.
	if ( ! Orchestra\Core::acl()->can('manage-users'))
	{
		if (Auth::guest())
		{
			Session::flash('orchestra.redirect', Input::get('redirect'));
			return Redirect::to(handles('orchestra::login'));
		}

		return Redirect::to(handles('orchestra'));
	}
});

Route::filter('orchestra::manage', function ()
{
	// Redirect the user to login page if user is not logged in.
	if ( ! Orchestra\Core::acl()->can('manage-orchestra'))
	{
		if (Auth::guest())
		{
			Session::flash('orchestra.redirect', Input::get('redirect'));
			return Redirect::to(handles('orchestra::login'));
		}

		return Redirect::to(handles('orchestra'));
	}
});

Route::filter('orchestra::allow-registration', function ()
{
	$memory = Orchestra\Core::memory();

	// Return 404 if registration is not enabled.
	if ( ! $memory->get('site.users.registration', false)) 
	{
		return Response::error('404');
	}
});

Route::filter('orchestra::installed', function ()
{
	// we should run installer when the system detect it's already running
	// or installed.
	if ( ! Orchestra\Installer::installed())
	{
		return Redirect::to_action("orchestra::installer@index");
	}
});

Route::filter('orchestra::csrf', function()
{
	if (Request::forged()) return Response::error('500');
});
