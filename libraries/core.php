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
			static::$cached['memory'] = Memory::make('fluent.orchestra_options');

			// Initiate ACL class with available memory.
			static::$cached['acl']    = Acl::make('orchestra', static::$cached['memory']);

			$users = Model\User::all();

			if (empty($users))
			{
				throw new Exception('User table is empty');
			}

			// In any event where Memory failed to load, we should set Installation status 
			// to false routing for installation is enabled.
			Installer::$status = true;
		}
		catch (Exception $e)
		{

		}

		static::$initiated = true;
	}

	/**
	 * Get memory instance for Orchestra
	 *
	 * @static
	 * @access public
	 * @return Hybrid\Memory
	 */
	public static function memory()
	{
		return isset(static::$cached['memory']) ? static::$cached['memory'] : Memory::make('runtime');
	}

	/**
	 * Get Acl instance for Orchestra
	 *
	 * @static
	 * @access public
	 * @return Hybrid\Acl
	 */
	public static function acl()
	{
		return static::$cached['acl'];
	}
}