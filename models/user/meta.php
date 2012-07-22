<?php namespace Orchestra\Model\User;

use \Eloquent;

class Meta extends Eloquent
{
	public static $table = 'user_meta';

	public function users()
	{
		return $this->belongs_to('Orchestra\Model\User', 'user_id');
	}
	
}