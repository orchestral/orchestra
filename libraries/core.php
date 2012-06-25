<?php namespace Orchestra;

use \Exception, 
	Hybrid\Acl,
	Hybrid\Memory;

class Core
{
	/**
	 * Core initiated status
	 *
	 * @static
	 * @access  protected
	 * @var     boolean
	 */
	protected static $initiated = false;

	/**
	 * Cached instances for Orchestra
	 * 
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $cached = array();

	/**
	 * Start Orchestra\Core
	 *
	 * @static
	 * @access public
	 * @return void
	 * @throws Exception If memory instance is not available (database not set yet)
	 */
	public static function start()
	{
		// avoid current method from being called more than once.
		if (true === static::$initiated) return ;

		try 
		{
			// Initiate Memory class
			static::$cached['memory'] = Memory::make('fluent.orchestra');

			// Initiate ACL class with available memory.
			static::$cached['acl']    = Acl::make('orchestra', static::$cached['memory']);
		}
		catch (Exception $e)
		{
			// In any event where Memory failed to load, we should set Installation status to 
			// false so routing for installation is enabled.
			Installer::$status = false;
		}

		static::$initiated = true;
	}

	/**
	 * Get memory instance for Orchestra
	 * 
	 * @return Hybrid\Memory
	 */
	public static function memory()
	{
		return static::$cached['memory'];
	}
}