<?php namespace Orchestra\Model;

use \Eloquent;

class Role extends Eloquent {

	/**
	 * Return pair of id and name array for all roles
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function pair()
	{
		$data = array();

		foreach (static::all() as $role)
		{
			$data[$role->id] = $role->name;
		}

		return $data;
	}

	/**
	 * Has Many and Belongs To `users` table using pivot table
	 * `user_roles`.
	 *
	 * @access public
	 * @return Orchestra\Model\User
	 */
	public function users()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\User', 'user_roles');
	}

}
