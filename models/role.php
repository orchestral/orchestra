<?php namespace Orchestra\Model;

use \Eloquent;

class Role extends Eloquent {

	/**
	 * Has Many and Belongs To `users` table using pivot table `user_roles`.
	 *
	 * @access public
	 * @return Orchestra\Model\User
	 */
	public function users()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\User', 'user_roles');
	}
}
