<?php

/*
|--------------------------------------------------------------------------
| Installer
|--------------------------------------------------------------------------
|
 */
Route::any('(:bundle)/installer/?(:any)?', function ($action = 'index') 
{
	// disable Orchestra installer if the system detect it is 
	// already running/installed
	if (Orchestra\Installer::installed())
	{
		return Response::error('404');
	}

	return Orchestra\Controller::call("orchestra::installer@{$action}");
});