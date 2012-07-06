<?php namespace Orchestra;

use \Config, \Exception, 
	Hybrid\Acl, Hybrid\Memory;

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
		static::$cached['menu'] = Widget::make('menu.orchestra');

		// rebuild hybrid auth configuration
		Config::set('hybrid::auth.roles', function ($user_id, $roles)
		{
			// in situation config is not a closure, we will use a basic convention structure.
			$user = Model\User::with('roles')->find($user_id);

			foreach ($user->roles as $role)
			{
				array_push($roles, $role->name);
			}

			return $roles;
		});

		// avoid current method from being called more than once.
		if (true === static::$initiated) return ;

		try 
		{
			// Initiate Memory class
			static::$cached['memory'] = Memory::make('fluent.orchestra_options');

			// Initiate ACL class with available memory.
			static::$cached['acl']    = Acl::make('orchestra');

			$users = Model\User::all();

			if (empty($users))
			{
				throw new Exception('User table is empty');
			}

			static::$cached['acl']->attach(static::$cached['memory']);
			
			static::$cached['menu']->add('home')->title('Home')->link('orchestra');

			if (static::$cached['acl']->can('manage-orchestra'))
			{
				static::$cached['menu']->add('update', 'childof:home')->title('Updates')->link('orchestra');
				static::$cached['menu']->add('users')->title('Users')->link('orchestra/users');
			}

			// In any event where Memory failed to load, we should set Installation status 
			// to false routing for installation is enabled.
			Installer::$status = true;
		}
		catch (Exception $e) 
		{
			static::$cached['memory'] = Memory::make('runtime.orchestra');
			static::$cached['acl']    = Acl::make('orchestra');

			static::$cached['menu']->add('install')->title('Install')->link('orchestra/installer');
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
		return isset(static::$cached['memory']) ? static::$cached['memory'] : null;
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
		return isset(static::$cached['acl']) ? static::$cached['acl'] : null;
	}

	/**
	 * Get Menu instance for Orchestra
	 *
	 * @static
	 * @access public
	 * @return Hybrid\Acl
	 */
	public static function menu()
	{
		return static::$cached['menu'];
	}
}