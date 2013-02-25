<?php namespace Orchestra\Support;

use \RuntimeException;

abstract class Morph {
	
	/**
	 * Method prefix.
	 *
	 * @var string
	 */
	public static $prefix = '';

	/**
	 * Magic method to call passtru PHP functions.
	 */
	public static function __callStatic($method, $parameters)
	{
		if ( ! static::is_callable($method))
		{
			throw new RuntimeException("Unable to call [{$method}].");
		}

		return call_user_func_array(static::$prefix.$method, $parameters);
	}

	/**
	 * Determine of method is_callable().
	 *
	 * @static
	 * @access public
	 * @param  string   $method
	 * @return bool
	 */
	public static function is_callable($method)
	{
		return is_callable(static::$prefix.$method);
	}
}
