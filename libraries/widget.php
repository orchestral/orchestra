<?php namespace Orchestra;

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
	 * @return Widget\Driver
	 */
	public static function make($name, $config = array()) 
	{
		if (false === strpos($name, '.')) $name = $name.'.default';

		list($type, $_name) = explode('.', $name, 2);

		if ( ! isset(static::$instances[$name]))
		{
			switch ($type)
			{
				case 'menu' :
					static::$instances[$name] = new Widget\Menu($_name, $config);
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