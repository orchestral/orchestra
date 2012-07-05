<?php namespace Orchestra\Model\User;

use \Eloquent;

class Meta extends Eloquent
{
	public function users()
	{
		return $this->belongs_to('Orchestra\Model\User', 'user_id');
	}
	
}