<?php namespace Orchestra\Model;

use \Config,
	\DateTime,
	\DateTimeZone,
	\Eloquent,
	\Hash,
	Orchestra\Memory,
	User\Meta as User_Meta;

class User extends Eloquent {

	/**
	 * Available user statuses.
	 */
	const UNVERIFIED = 0;
	const VERIFIED   = 1;

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
	 * Get localtime information of the user.
	 *
	 * @access public
	 * @param  mixed    $datetime 
	 * @return DateTime
	 */
	public function localtime($datetime)
	{
		$user_id          = $this->get_attribute('id');
		$meta             = Memory::make('user');
		$default_timezone = Config::get('application.timezone', 'UTC');

		if ( ! ($datetime instanceof DateTime))
		{
			$datetime = new DateTime(
				$datetime, 
				new DateTimeZone($default_timezone)
			);
		}

		$user_timezone = $meta->get("timezone.{$user_id}", $default_timezone);

		$datetime->setTimeZone(new DateTimeZone($user_timezone));

		return $datetime;
	}

	/**
	 * Get user timezone.
	 *
	 * @access public
	 * @return string
	 */
	public function timezone()
	{
		$user_id = $this->get_attribute('id');
		$meta    = Memory::make('user');

		return $meta->get("timezone.{$user_id}", Config::get('application.timezone', 'UTC'));
	}

	/**
	 * Search user based on keyword as roles.
	 *
	 * @static
	 * @access public 	
	 * @param  string   $keyword
	 * @param  array    $roles
	 * @return Orchestra\Model\User
	 */
	public static function search($keyword = '', $roles = array())
	{
		$users = static::with('roles')->where_not_null('users.id');

		if ( ! empty($roles))
		{
			$users->join('user_roles', function ($join) use ($roles)
			{
				$join->on('users.id', '=', 'user_roles.user_id');

			})->where_in('user_roles.role_id', $roles);
		}

		if ( ! empty($keyword))
		{
			$users->where(function ($query) use ($keyword)
			{
				$query->where('email', 'LIKE', $keyword)
					->or_where('fullname', 'LIKE', $keyword);
			});
		}

		return $users;
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
