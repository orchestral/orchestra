<?php

class Orchestra 
{
	/**
	 * Shorthand for calling Orchestra\Core methods.
	 *
	 * @static
	 * @access public
	 * @param  string $method
	 * @param  array $parameters
	 * @return Orchestra\Core
	 */
	public static function __callStatic($method, $parameters)
	{
		return forward_static_call_array(array("Orchestra\Core", $method), $parameters);
	}
}