<?php namespace Orchestra;

use \Bundle, \IoC, FileSystemIterator as fIterator;

class Extension 
{
	/**
	 * List of extensions
	 * 
	 * @var array
	 */
	protected static $extensions = array();

	/**
	 * Load an extension by running it's start-up script.
	 *
	 * If the extension has already been started, no action will be taken.
	 *
	 * @static
	 * @access public
	 * @param  string  $name
	 * @param  array   $config
	 * @return void
	 */
	public static function start($name, $config = array())
	{
		$default = array('handles' => null, 'auto' => false);
		$name    = $name ?: null;
		$config  = (array) $config;

		if ( ! is_string($name)) return;

		// Register extension and auto start it only if it's not registered
		if ( ! Bundle::exists($name))
		{
			Bundle::register($name, $config);
			Bundle::start($name);
		}

		// by now, extension should already exist as an extension. We should
		// be able start orchestra.php starter file on each bundles.
		if (is_file($file = Bundle::path($name).'orchestra.php'))
		{
			include_once $file;
		}

		static::$extensions[$name] = array_merge($default, $config);
	}

	/**
	 * Check if extension is started
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @return bool
	 */
	public static function started($name)
	{
		return (array_key_exists($name, static::$extensions));
	}

	/**
	 * Get an option for a given extension.
	 *
	 * @param  string  $name
	 * @param  string  $option
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function option($name, $option, $default = null)
	{
		$extension = static::$extensions[$name];

		if (is_null($extension))
		{
			return value($default);
		}

		return array_get($extension, $option, $default);
	}


	/**
	 * Detect all of the extensions for orchestra
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function detect()
	{
		$extensions = array();
		$memory     = Core::memory();

		if (is_file(path('app').'/orchestra.json'))
		{
			$extensions[DEFAULT_BUNDLE] = json_decode(file_get_contents(path('app').'/orchestra.json'));
		}

		$directory = path('bundle');

		$items = new fIterator($directory, fIterator::SKIP_DOTS);

		foreach ($items as $item)
		{
			if ($item->isDir())
			{
				if (is_file($item->getRealPath().'/orchestra.json'))
				{
					$extensions[$item->getFilename()] = json_decode(file_get_contents($item->getRealPath().'/orchestra.json'));
				}
			}
		}

		$cached = array();

		// we should cache extension to be stored to Hybrid\Memory to avoid 
		// over usage of database space
		foreach ($extensions as $name => $extension)
		{
			$ext_name   = isset($extension->name) ? $extension->name : null;
			$ext_config = isset($extension->config) ? $extension->config : array();

			if (is_null($ext_name)) continue;

			$cached[$name] = array(
				'name'   => $ext_name,
				'config' => (array) $ext_config,
			);
		}

		$memory->put('extensions.available', $cached);

		return $extensions;
	}

	/**
	 * Activate an extension
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @return void
	 */
	public static function activate($name)
	{
		$memory    = Core::memory();
		$available = (array) $memory->get('extensions.available', array());
		$active    = (array) $memory->get('extensions.active', array());

		if (isset($available[$name]))
		{
			$active[$name] = (array) $available[$name]['config'];

			// we should also start the bundle
			static::start($name, $active[$name]);

			if (IoC::registered('task: orchestra.migrator'))
			{
				IoC::resolve('task: orchestra.migrator', array('migrate', $name));
			}

			if (IoC::registered('task: orchestra.publisher'))
			{
				IoC::resolve('task: orchestra.publisher', array($name));
			}
		}

		$memory->put('extensions.active', $active);
	}

	/**
	 * Determine whether the requested extension is active
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @return bool
	 */
	public static function activated($name)
	{
		$memory    = Core::memory();
		$active    = (array) $memory->get('extensions.active', array());

		return (in_array($name, $active));
	}

	/**
	 * Deactivate an extension
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @return void
	 */
	public static function deactivate($name)
	{
		$memory  = Core::memory();
		$current = (array) $memory->get('extensions.active', array());
		$active  = array();

		foreach ($current as $extension => $config)
		{
			if (is_numeric($extension)) 
			{
				$extension = $config;
				$config    = array();
			}

			if ($extension !== $name)
			{
				$active[$extension] = $config;
			}
		}

		$memory->put('extensions.active', $active);
	}

	/**
	 * Get all of the installed extensions for the application.
	 *
	 * @return array
	 */
	public static function all()
	{
		return static::$extensions;
	}
}