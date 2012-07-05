<?php namespace Orchestra\Model;

use \Eloquent;

class Role extends Eloquent 
{
	public function users()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\User', 'user_roles');
	}

}