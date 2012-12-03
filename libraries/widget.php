<?php namespace Orchestra;

use \InvalidArgumentException;

class Widget {

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
	 * @param  string   $driver  a string identifying the widget
	 * @param  arrat    $config  a configuration array
	 * @return Widget\Driver
	 */
	public static function make($driver, $config = array())
	{
		// the "driver" string might not contain a using "{$type}.{$name}" 
		// format, if $name is missing let use "default" as $name.
		if (false === strpos($driver, '.')) $driver = $driver.'.default';

		list($type, $name) = explode('.', $driver, 2);

		if ( ! isset(static::$instances[$driver]))
		{
			switch ($type)
			{
				case 'menu' :
					static::$instances[$driver] = new Widget\Menu($name, $config);
					break;
				case 'pane' :
					static::$instances[$driver] = new Widget\Pane($name, $config);
					break;
				case 'placeholder' :
					static::$instances[$driver] = new Widget\Placeholder($name, $config);
					break;
				default :
					throw new InvalidArgumentException(
						"Requested Orchestra\Widget Driver [{$type}] does not exist."
					);
			}
		}

		return static::$instances[$driver];
	}

	/**
	 * Orchestra\Widget doesn't support a construct method
	 *
	 * @access  protected
	 */
	protected function __construct() {}
}
