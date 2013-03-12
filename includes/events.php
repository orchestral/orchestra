<?php

/*
|--------------------------------------------------------------------------
| Orchestra Events Listener
|--------------------------------------------------------------------------
|
| Lets listen to when Orchestra bundle is started.
|
*/

Event::listen('laravel.done', function ()
{
	Orchestra\Core::shutdown();
});

Event::listen('orchestra.started: backend', function ()
{
	Orchestra\View::$theme = 'backend';
});

Event::listen('orchestra.done: backend', function ()
{
	Orchestra\Messages::shutdown();
});

/*
|--------------------------------------------------------------------------
| Event listen to sync roles
|--------------------------------------------------------------------------
*/

Event::listen('eloquent.saving: Orchestra\Model\Role', function ($role)
{
	if ($role->exists)
	{
		$old_name = $role->original['name'];
		Orchestra\Acl::rename_role($old_name, $role->name);
	}
	else
	{
		Orchestra\Acl::add_role($role->name);
	}
});

Event::listen('eloquent.deleting: Orchestra\Model\Role', function ($role)
{
	Orchestra\Acl::remove_role($role->name);
});