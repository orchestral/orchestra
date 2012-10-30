<?php namespace Orchestra;

use \Config, \Exception, \Event, \IoC,
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
		// avoid current method from being called more than once.
		if (true === static::$initiated) return ;

		// Make Menu instance
		static::$cached['orchestra_menu'] = Widget::make('menu.orchestra');
		
		// Make Menu instance for frontend application
		static::$cached['app_menu'] = Widget::make('menu.application');

		// Make ACL instance
		static::$cached['acl'] = Acl::make('orchestra');

		// First, we need to ensure that Hybrid\Acl is compliance with 
		// our Eloquent Model, This would overwrite the default configuration
		Config::set('hybrid::auth.roles', function ($user, $roles)
		{
			// Check if user is null, there roles shouldn't be available, returning 
			// null would allow any other Event listening to the same id to overwrite
			// this.
			if (is_null($user)) return ;

			foreach ($user->roles()->get() as $role)
			{
				array_push($roles, $role->name);
			}

			return $roles;
		});

		try 
		{
			// Initiate Memory class
			static::$cached['memory'] = IoC::resolve('orchestra.memory');

			if (is_null(static::$cached['memory']->get('site.name')))
			{
				throw new Exception('Installation is not completed');
			}

			// In event where we reach this point, we can consider no 
			// exception has occur, we should be able to compile acl and menu 
			// configuration
			static::$cached['acl']->attach(static::$cached['memory']);

			// In any event where Memory failed to load, we should set 
			// Installation status to false routing for installation is 
			// enabled.
			Installer::$status = true;

			static::loader();
			static::extensions();
		}
		catch (Exception $e) 
		{
			// In any case where Exception is catched, we can be assure that
			// Installation is not done/completed, in this case we should use 
			// runtime/in-memory setup
			static::$cached['memory'] = Memory::make('runtime.orchestra');

			static::$cached['orchestra_menu']->add('install')->title('Install')->link(handles('orchestra::installer'));
		}

		// localize memory variable
		$memory = static::$cached['memory'];
		
		IoC::singleton('orchestra.theme: backend', function() use ($memory)
		{
			return new Theme($memory->get('site.theme.backend', function () use ($memory)
			{
				return $memory->put('site.theme.backend', 'default');
			}));
		});

		IoC::singleton('orchestra.theme: frontend', function() use ($memory)
		{
			return new Theme($memory->get('site.theme.frontend', function () use ($memory)
			{
				return $memory->put('site.theme.frontend', 'default');
			}));
		});

		Event::fire('orchestra.started');

		static::$initiated = true;
	}

	/**
	 * Stop Orchestra\Core
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function done()
	{	
		static::$initiated = false;
		static::$cached = array();

		// Only do this on installed application
		if (false === Installer::$status) return;
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
	public static function menu($type = 'orchestra')
	{
		return static::$cached["{$type}_menu"] ?: null;
	}

	/**
	 * Load Extensions for Orchestra
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function extensions()
	{
		$memory     = Core::memory();
		$availables = (array) $memory->get('extensions.available', array());
		$actives    = (array) $memory->get('extensions.active', array());

		foreach ($actives as $extension => $config)
		{
			if (is_numeric($extension))
			{
				$extension = $config;
				$config    = array();

				if (isset($availables[$extension]))
				{
					$config = (array) $availables[$extension]['config'];
				}
			}

			if (isset($availables[$extension]))
			{
				Extension::start($extension, $config);
			}
		}

		// Resynchronize all active extension, this to ensure all configuration
		// is standard.
		$memory->put('extensions.active', Extension::all());
	}

	/**
	 * Loader for Orchestra
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function loader()
	{
		// localize the variable, and ensure it by references.
		$menu   = Core::menu('orchestra');
		$acl    = Core::acl();

		// Add basic menu.
		$menu->add('home')
			->title(__('orchestra::title.home.list')->get())
			->link(handles('orchestra'));

		// Multiple event listener for Backend (administrator panel)
		Event::listen('orchestra.started: backend', function () use ($menu, $acl)
		{
			// Add menu when user can manage users
			if ($acl->can('manage-users'))
			{
				$menu->add('users')
					->title(__('orchestra::title.users.list')->get())
					->link(handles('orchestra::users'));

				$menu->add('add-users', 'child_of:users')
					->title(__('orchestra::title.users.create')->get())
					->link(handles('orchestra::users/view'));
			}

			// Add menu when user can manage orchestra
			if ($acl->can('manage-orchestra'))
			{
				$menu->add('extensions', 'after:home')
					->title(__('orchestra::title.extensions.list')->get())
					->link(handles('orchestra::extensions'));

				$resources = Resources::all();

				if ( ! empty($resources))
				{
					$menu->add('resources', 'after:extensions')
						->title(__('orchestra::title.resources.list')->get())
						->link(handles('orchestra::resources'));

					foreach (Resources::all() as $name => $resource)
					{
						$menu->add($name, 'child_of:resources')
							->title($resource->name)
							->link(handles("orchestra::resources/{$name}"));
					}
				}

				$menu->add('settings')
					->title(__('orchestra::title.settings.list')->get())
					->link(handles('orchestra::settings'));

				$menu->add('settings', 'child_of:settings')
					->title(__('orchestra::title.settings.upgrade')->get())
					->link(handles('orchestra::settings/upgrade'));
			}
		});
	}
}