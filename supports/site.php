<?php namespace Orchestra\Support;

use \DateTime, 
	\DateTimeZone,
	\Auth as A, 
	\Config;

class Site {

	/**
	 * Data for site.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * Get a site value.
	 *
	 * @static
	 * @access public 	
	 * @param  string   $key
	 * @param  mixed    $default
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		return array_get(static::$items, $key, $default);
	}

	/**
	 * Set a site value.
	 *
	 * @static
	 * @access public 	
	 * @param  string   $key
	 * @param  mixed    $value
	 * @return mixed
	 */
	public static function set($key, $value = null)
	{
		return array_set(static::$items, $key, $value);
	}

	/**
	 * Check if site key has a value.
	 *
	 * @static
	 * @access public 	
	 * @param  string   $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ! is_null(static::get($key));
	}

	/**
	 * Remove a site key.
	 *
	 * @static
	 * @access public
	 * @param  string   $key
	 * @return void
	 */
	public static function forget($key)
	{
		return array_forget(static::$items, $key);
	}

	/**
	 * Convert given time to user localtime, however if it a guest user 
	 * return based on default timezone.
	 *
	 * @static
	 * @access public
	 * @param  mixed    $datetime
	 * @return DateTime
	 */
	public static function localtime($datetime)
	{
		if ( ! ($datetime instanceof DateTime))
		{
			$datetime = new DateTime(
				$datetime, 
				new DateTimeZone(Config::get('application.timezone', 'UTC'))
			);
		}

		if (Auth::guest()) return $datetime;

		return Auth::user()->localtime($datetime);
	}
}