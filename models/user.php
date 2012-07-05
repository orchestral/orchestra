<?php namespace Orchestra\Model;

use \Eloquent;

class User extends Eloquent 
{
	public function meta()
	{
		return $this->has_many('Orchestra\Model\User\Meta');
	}
	
	public function roles()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\Role', 'user_roles');
	}
}