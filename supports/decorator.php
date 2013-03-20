<?php namespace Orchestra\Support;

use BadMethodCallException;

class Decorator {
	
	/**
	 * The registered custom macros.
	 *
	 * @var array
	 */
	public static $macros = array();

	/**
	 * Registers a custom macro.
	 *
	 * @param  string   $name
	 * @param  Closure  $macro
	 * @return void
	 */
	public static function macro($name, $macro)
	{
		static::$macros[$name] = $macro;
	}

	/**
	 * Dynamically handle calls to custom macros.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		if (isset(static::$macros[$method]))
		{
			return call_user_func_array(static::$macros[$method], $parameters);
		}

		throw new BadMethodCallException("Method [$method] does not exist.");
	}
}
