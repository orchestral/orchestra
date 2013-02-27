<?php namespace Orchestra\Support;

class Acl {
	
	/**
	 * Acl initiated status
	 *
	 * @static
	 * @access  protected
	 * @var     boolean
	 */
	protected static $initiated = false;

	/**
	 * Cache ACL instance so we can reuse it on multiple request. 
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $instances = array();

	/**
	 * Initiate a new Acl instance.
	 * 
	 * @static
	 * @access  public
	 * @param   string        $name
	 * @param   Memory\Driver $memory
	 * @return  self
	 */
	public static function make($name = null, Memory\Driver $memory = null)
	{
		if (is_null($name)) $name = 'default';

		if ( ! isset(static::$instances[$name]))
		{
			static::$instances[$name] = new Acl\Container($name, $memory);
		}

		return static::$instances[$name];
	}

	/**
	 * Register an Acl instance with Closure.
	 * 
	 * @static
	 * @access  public
	 * @param   string  $name
	 * @param   Closure $callback
	 * @return  self
	 */
	public static function register($name, $callback = null)
	{
		if (is_callable($name))
		{
			$callback = $name;
			$name     = null;
		}

		$instance = static::make($name);

		$callback($instance);

		return $instance;
	}

	/**
	 * Manipulate and synchronize roles.
	 *
	 * @static
	 * @access public
	 * @
	 */
	public static function __callStatic($method, $parameters)
	{
		$result = array();

		if (preg_match('/^(add|fill|rename|has|get|remove)_(role)(s?)$/', $method, $matches))
		{
			$operation = $matches[1];
			$type      = $matches[2].'s';
			$multi_add = (isset($matches[3]) and $matches[3] === 's' and $operation === 'add');

			( !! $multi_add) and $operation = 'fill';

			foreach (static::$instances as $acl)
			{
				$result[] = $acl->passthru($type, $operation, $parameters);

				if ('has' !== $operation) $acl->sync();
			}
		}

		return $result;
	}

	/**
	 * Shutdown Orchestra\Support\Acl
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function shutdown()
	{
		// Re-sync before shutting down.
		foreach(static::$instances as $acl) $acl->sync();

		Memory::shutdown();

		static::$initiated = false;
		static::$instances = array();
	}

	/**
	 * Get all Orchestra\Support\Acl instances.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function all()
	{
		return static::$instances;
	}

}