<?php

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

/*
|--------------------------------------------------------------------------
| Event listen to remove timezone Session
|--------------------------------------------------------------------------
*/

Event::listen('eloquent.saving: Orchestra\Model\User\Meta', function ($meta)
{
	if ($meta->name !== 'timezone') return null;

	$user_id = $meta->user_id;

	Cache::delete("orchestra.user.localtime.{$user_id}");
});