<?php namespace Orchestra\Model;

use \Eloquent;

class User extends Eloquent 
{
	public function roles()
	{
		return $this->belongs_to_and_has_many('Orchestra\Model\Role');
	}
}