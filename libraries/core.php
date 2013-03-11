<?php namespace Orchestra;

use \Asset,
	\Auth,
	\Config,
	\Exception,
	\Event,
	\IoC,
	\Input,
	\Session,
	\URI;

class Core {

	/**
	 * Core initiated status
	 *
	 * @static
	 * @access  protected
	 * @var     boolean
	 */
	protected static $initiated = false;

	/**
	 * Using safe mode?
	 *
	 * @static
	 * @access protected
	 * @var    boolean
	 */
	protected static $safe_mode = false;
	

	/**
	 * Cached instances for Orchestra
	 *
	 * @static
	 * @access  protected
	 * @var     array
	 */
	protected static $cached = array();

	/**
	 * Start Orchestra
	 *
	 * @static
	 * @access public
	 * @return void
	 * @throws Exception    If memory instance is not available
	 *                      (database not set yet)
	 */
	public static function start()
	{
		// avoid current method from being called more than once.
		if (true === static::$initiated) return ;

		// Make Menu instance for backend and frontend appliction
		static::$cached['orchestra_menu'] = Widget::make('menu.orchestra');
		static::$cached['app_menu']       = Widget::make('menu.application');

		// Make ACL instance for Orchestra
		static::$cached['acl'] = Acl::make('orchestra');

		try
		{
			// Initiate Memory class from IoC, this to allow advanced user
			// to use other implementation if there is a need for it.
			static::$cached['memory'] = IoC::resolve('orchestra.memory');

			if (is_null(static::$cached['memory']->get('site.name')))
			{
				throw new Exception('Installation is not completed');
			}

			// In event where we reach this point, we can consider no
			// exception has occur, we should be able to compile acl and
			// menu configuration
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
			// Installation is not done/completed, in this case we should
			// use runtime/in-memory setup
			static::$cached['memory'] = Memory::make('runtime.orchestra');
			static::$cached['memory']->put('site.name', 'Orchestra');

			static::$cached['orchestra_menu']->add('install')
				->title('Install')
				->link(handles('orchestra::installer'));

			Installer::$status = false;
		}

		Theme::start();
		Event::fire('orchestra.started');

		static::$initiated = true;
	}

	/**
	 * Shutdown Orchestra
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function shutdown()
	{
		Extension::shutdown();
		Acl::shutdown();

		// Orchestra is shutdown, let notify everyone.
		Event::fire('orchestra.done');

		static::$initiated = false;
		static::$cached    = array();

		// Only do this on installed application
		if (false === Installer::$status) return;
	}

	/**
	 * Get memory instance for Orchestra
	 *
	 * @static
	 * @access public
	 * @return Orchestra\Memory
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
	 * @return Orchestra\Acl
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
	 * @return Orchestra\Acl
	 */
	public static function menu($type = 'orchestra')
	{
		return isset(static::$cached["{$type}_menu"]) ? static::$cached["{$type}_menu"] : null;
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
		if ( !! static::check_safe_mode()) return ;

		$memory     = Core::memory();
		$availables = (array) $memory->get('extensions.available', array());
		$actives    = (array) $memory->get('extensions.active', array());

		foreach ($actives as $extension => $config)
		{
			if (isset($availables[$extension]))
			{
				Extension::start($extension, $config);
			}
		}

		// Resynchronize all active extension, this to ensure all
		// configuration is standard.
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
		// Set application language.
		static::detect_language();

		// localize the variable, and ensure it by references.
		$acl  = static::acl();
		$menu = static::menu('orchestra');

		// Add basic menu.
		$menu->add('home')
			->title(__('orchestra::title.home')->get())
			->link(handles('orchestra'));

		// Multiple event listener for Backend (administrator panel)
		Event::listen('orchestra.done: backend', function () use ($acl, $menu)
		{
			// Add menu when logged-user user has authorization to
			// `manage users`
			if ($acl->can('manage-users'))
			{
				$menu->add('users')
					->title(__('orchestra::title.users.list')->get())
					->link(handles('orchestra::users'));

				$menu->add('add-users', 'child_of:users')
					->title(__('orchestra::title.users.create')->get())
					->link(handles('orchestra::users/view'));
			}

			// Add menu when logged-in user has authorization to
			// `manage orchestra`
			if ($acl->can('manage-orchestra'))
			{
				$menu->add('extensions', 'after:home')
					->title(__('orchestra::title.extensions.list')->get())
					->link(handles('orchestra::extensions'));

				$menu->add('settings')
					->title(__('orchestra::title.settings.list')->get())
					->link(handles('orchestra::settings'));
			}

			// If user aren't logged in, we should stop at this point,
			// Resources  only be available to logged-in user.
			if (Auth::guest()) return;

			$resources = Resources::all();

			// Resources menu should only be appended if there is actually
			// resources to be displayed.
			if ( ! empty($resources))
			{
				$menu->add('resources', 'after:extensions')
					->title(__('orchestra::title.resources.list')->get())
					->link(handles('orchestra::resources'));

				foreach ($resources as $name => $resource)
				{
					if (false === value($resource->visible)) continue;

					$menu->add($name, 'child_of:resources')
						->title($resource->name)
						->link(handles("orchestra::resources/{$name}"));
				}
			}
		});
	}

	/**
	 * Determine whether current request is in safe mode or not.
	 *
	 * @static
	 * @access protected
	 * @return boolean
	 */
	protected static function check_safe_mode()
	{
		if (Input::get('safe_mode') == '0')
		{
			Session::forget('safe_mode');
			return static::$safe_mode = false;
		}

		$session = Session::get('safe_mode', function ()
		{
			if (Input::get('safe_mode') != '1') return 'N';
			
			Session::put('safe_mode', 'Y');
			return 'Y';
		});

		return static::$safe_mode = ($session === 'Y');
	}

	/**
	 * Detect locale from URI
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function detect_language()
	{
		$uri         = URI::current();
		$languages   = Config::get('application.languages', array());
		$languages[] = Config::get('application.language');

		foreach ($languages as $language)
		{
			if (preg_match("#^{$language}(?:$|/)#i", $uri))
			{
				Config::set('application.language', $language);
				
				$uri = trim(substr($uri, strlen($language)), '/'); break;
			}
		}

		if ($uri == '') $uri = '/';

		URI::$uri = $uri;
	}
}
