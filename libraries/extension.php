<?php namespace Orchestra;

use \Bundle, \IoC, FileSystemIterator as fIterator;

class Extension 
{
	/**
	 * List of started extensions
	 * 
	 * @var array
	 */
	protected static $started = array();

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
		$name   = $name ?: null;
		$config = (array) $config;

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

		static::$started[] = $name;
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
		return (in_array($name, static::$started));
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
			$cached[$name] = array(
				'name'   => $extension->name,
				'config' => $extension->config
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
			array_push($active, $name);

			// we should also start the bundle
			static::start($name, $available[$name]['config']);

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

		foreach ($current as $extension)
		{
			if ($extension !== $name)
			{
				array_push($active, $extension);
			}
		}

		$memory->put('extensions.active', $active);
	}
}