<?php namespace Orchestra\Model;

use \Config,
	\Eloquent;

class Role extends Eloquent {

	/**
	 * Get default roles for Orchestra Platform
	 *
	 * @static
	 * @access public
	 * @return self
	 */
	public static function admin()
	{
		return static::find(Config::get('orchestra::orchestra.default_role'));
	}

	/**
	 * Get default member roles for Orchestra Platform
	 *
	 * @static
	 * @access public
	 * @return self
	 */
	public static function member()
	{
		return static::find(Config::get('orchestra::orchestra.member_role'));
	}

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
