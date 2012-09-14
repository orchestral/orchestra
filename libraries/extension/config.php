<?php namespace Orchestra\Extension;

use Laravel\Config as Laravel_Config, Orchestra\Core;

class Config
{ 
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
			if (isset($config[$current]))
			{
				Laravel_Config::set($default, $config[$current]);
			}
			
			$config[$current] = Laravel_Config::get($default);
		}

		$config = $memory->put("extension_{$name}", $config);
	}
}