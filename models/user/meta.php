<?php namespace Orchestra\Model\User;

use \Eloquent;

class Meta extends Eloquent {

	/**
	 * Overwrite table name
	 *
	 * @var string
	 */
	public static $table = 'user_meta';

	/**
	 * Belongs To `users` table.
	 *
	 * @access public
	 * @return Orchestra\Model\User
	 */
	public function users()
	{
		return $this->belongs_to('Orchestra\Model\User', 'user_id');
	}

	/**
	 * Get all meta data belong to a user.
	 *
	 * @static
	 * @access public
	 * @param  int      $user_id
	 * @return Orchestra\Model\User\Meta
	 */
	public static function user($user_id)
	{
		return static::where('user_id', '=', (int) $user_id);
	}

	/**
	 * Retun a meta data belong to a user.
	 * 
	 * @static
	 * @access public
	 * @param  string   $name
	 * @param  int      $user_id
	 * @return Orchestra\Model\User\Meta
	 */
	public static function name($name, $user_id)
	{
		return static::user($user_id)
			->where('name', '=', $name)
			->first();
	}
}
