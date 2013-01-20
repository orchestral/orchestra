<?php namespace Orchestra\Model;

use \Config,
	\DateTime,
	\DateTimeZone,
	\Eloquent,
	\Hash,
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
		$default_timezone = Config::get('timezone', 'UTC');

		if ( ! ($datetime instanceof DateTime))
		{
			$datetime = new DateTime(
				$datetime, 
				new DateTimeZone($default_timezone)
			);
		}
		$user_timezone = Cache::get("orchestra.user.localtime.{$user_id}", function() 
			use ($user_id, $default_timezone)
		{
			$user_timezone = User_Meta::name('timezone', $user_id);

			if (is_null($user_timezone)) $user_timezone = $default_timezone;
			Cache::put("orchestra.user.localtime.{$user_id}", $user_timezone);

			return $user_timezone;
		});

		$datetime->setTimeZone(new DateTimeZone($user_timezone));

		return
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
