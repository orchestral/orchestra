<?php namespace Orchestra\Model;

use \Eloquent;

class Role extends Eloquent 
{
	public function users()
	{
		return $this->belongs_to_and_has_many('Orchestra\Model\User');
	}
}
}