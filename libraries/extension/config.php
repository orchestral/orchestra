<?php namespace Orchestra\Extension;

use Laravel\Config as C,
	Orchestra\Core;

class Config {

	/**
	 * Map configuration to allow orchestra to store it in database
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @param  array    $maps
	 * @return void
	 */
	public static function map($name, $maps)
	{
		$memory = Core::memory();
		$config = $memory->get("extension_{$name}", array());

		foreach ($maps as $current => $default)
		{
			isset($config[$current]) and C::set($default, $config[$current]);

			$config[$current] = C::get($default);
		}

		$config = $memory->put("extension_{$name}", $config);
	}
}
