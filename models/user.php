<?php namespace Orchestra\Model;

use \Eloquent;

class User extends Eloquent {

	/**
	 * Has Many relationship to `user_meta` table.
	 *
	 * @access public
	 * @return Orchestra\Model\User\Meta
	 */
	public function meta()
	{
		return $this->has_many('Orchestra\Model\User\Meta');
	}

	/**
	 * Has Many and Belongs To `roles` table using pivot table `user_roles`.
	 *
	 * @access public
	 * @return Orchestra\Model\Role
	 */
	public function roles()
	{
		return $this->has_many_and_belongs_to('Orchestra\Model\Role', 'user_roles');
	}

	/**
	 * Setter for password attributes.
	 * 
	 * @access public 
	 * @param  string   $password
	 * @return void
	 */
	public function set_password($password)
	{
		$this->set_attribute('password', Hash::make($password));
	}
}
