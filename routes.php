<?php

/*
|--------------------------------------------------------------------------
| Installer
|--------------------------------------------------------------------------
|
| Run installation route when Orchestra is not installed yet.
 */
Route::any('(:bundle)/installer/?(:any)?', function ($action = 'index') 
{
	// we should disable this routing when the system 
	// detect it's already running/installed.
	if (Orchestra\Installer::installed()) return Response::error('404');

	// Otherwise, install it right away.
	return Controller::call("orchestra::installer@{$action}");
});

/*
|--------------------------------------------------------------------------
| Default Routing
|--------------------------------------------------------------------------
 */
Route::any('(:bundle)', array('before' => 'orchestra::auth', function ()
{
	// Display the dashboard
	return Controller::call('orchestra::dashboard@index');
}));

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
| Detects all controller under Orchestra bundle and register it to routing
 */
Route::controller(array('orchestra::admin.home'));

/*
|--------------------------------------------------------------------------
| Route Filtering
|--------------------------------------------------------------------------
|
 */
Route::filter('orchestra::auth', function ()
{
	if (Auth::guest()) return Redirect::to('orchestra/login');
});