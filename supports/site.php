<?php namespace Orchestra\Support;

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
	 * @return mixed
	 */
	public static function has($key)
	{
		return ! is_null(static::get($key));
	}


}