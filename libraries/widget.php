<?php namespace Orchestra;

use \Exception;

class Widget 
{
	/**
	 * Cache widget instance so we can reuse it
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Make a new Widget instance
	 *
	 * @static
	 * @access public
	 * @param  string   $name    a string identifying the widget
	 * @param  arrat    $config  a configuration array
	 * @return Widget\Driver
	 */
	public static function make($name, $config = array()) 
	{
		if (false === strpos($name, '.')) $name = $name.'.default';

		list($type, $driver) = explode('.', $name, 2);

		if ( ! isset(static::$instances[$name]))
		{
			switch ($type)
			{
				case 'menu' :
					static::$instances[$name] = new Widget\Menu($driver, $config);
					break;
				case 'pane' :
					static::$instances[$name] = new Widget\Pane($driver, $config);
					break;
				default :
					throw new Exception("Requested Orchestra\Widget Driver [{$type}] does not exist.");
			}
		}

		return static::$instances[$name];
	}

	/**
	 * Orchestra\Widget doesn't support a construct method
	 *
	 * @access  protected
	 */
	protected function __construct() {}
}