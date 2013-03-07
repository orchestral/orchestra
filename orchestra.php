<?php

class Orchestra
{
	const VERSION = '1.1.2';

	/**
	 * Facade for Orchestra\Core.
	 *
	 * @static
	 * @access public
	 * @param  string   $method
	 * @param  array    $parameters
	 * @return Orchestra\Core
	 */
	public static function __callStatic($method, $parameters)
	{
		$forward_to = array("Orchestra\Core", $method);

		return forward_static_call_array($forward_to, $parameters);
	}
}
