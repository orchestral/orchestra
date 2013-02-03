<?php namespace Orchestra;

use \Closure, 
	\InvalidArgumentException,
	\RuntimeException;

class Widget {

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

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
			if (isset(static::$registrar[$type]))
			{
				$resolver = static::$registrar[$type];

				return static::$instances[$name] = $resolver($type, $config);
			}

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
	 * Register a third-party widget driver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	/**
	 * Orchestra\Widget doesn't support a construct method
	 *
	 * @access  public
	 * @throws  RuntimeException
	 */
	public function __construct() 
	{
		throw new RuntimeException("Orchestra\Widget doesn't support a construct method.");
	}
}
