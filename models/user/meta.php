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

}
