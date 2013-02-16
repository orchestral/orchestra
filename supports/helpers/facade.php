<?php namespace Orchestra\Support\Helpers;

use \InvalidArgumentException;

abstract class Facade {
	
	/**
	 * Define Helpers Facade prefix.
	 * 
	 * @var string
	 */
	protected static $prefix = '';

	/**
	 * Call magic method
	 */
	public function __callStatic($method, $parameters)
	{
		$callback = static::$prefix.$method;

		if ( ! is_callable($callback)
		{
			throw new InvalidArgumentException(
				"Method [{$callback}] is not callable."
			);
		}

		return call_user_func_array($callback, $parameters);
	}
}
