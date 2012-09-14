<?php namespace Orchestra;

use Exception;

class Resources
{
	/**
	 * The resources registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Register a new resource
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @param  string $controller Controller name
	 * @return void
	 */
	public static function register($name, $controller)
	{
		static::$registrar[$name] = $controller;
	}

	/**
	 * Call a resource controller and action.
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @param  string $action
	 * @param  array  $arguments
	 * @return Response
	 */
	public static function call($name, $action, $arguments)
	{
		if ( ! isset(static::$registrar[$name])) return false;
		
		$controller = static::$registrar[$name];

		return Controller::call("{$controller}@{$action}", $arguments);
	}
}