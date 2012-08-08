<?php namespace Orchestra\Model;

use \Eloquent;

class Role extends Eloquent 
{
	public static function pair()
	{
		$data = array();

		foreach (static::all() as $role) 
		{
			$data[$role->id] = $role->name;
		}

		return $data;
	}
	
	public function users()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\User', 'user_roles');
	}

}