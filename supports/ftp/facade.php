<?php namespace Orchestra\Support\FTP;

use Orchestra\Support\Morph;

class Facade extends Morph {

	/**
	 * Define Morph prefix.
	 *
	 * @var string
	 */
	public static $prefix = 'ftp_';

	/**
	 * Magic method to ftp methods.
	 */
	public static function fire($method, $parameters)
	{
		$result = null;

		if ( ! static::is_callable($method) 
			or ! $result = call_user_func_array(static::$prefix.$method, $parameters))
		{
			throw new RuntimeException("Failed to use {$method}.", $parameters);
		}

		return $result;
	}
}