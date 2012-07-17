<?php namespace Orchestra;

use FileSystemIterator as fIterator;

class Extension 
{
	protected static $started = array();

	/**
	 * Load an extension by running it's start-up script.
	 *
	 * If the extension has already been started, no action will be taken.
	 *
	 * @static
	 * @access public
	 * @param  string  $extension
	 * @return void
	 */
	public static function start($extension)
	{
		$name   = $extension->name ?: null;
		$config = $extension->config ?: array();

		if ( ! is_string($name)) return;

		// Register extension and auto start it only if it's not registered
		if ( ! Bundle::exists($name))
		{
			Bundle::register($name, $config);
			Bundle::start($name);
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

	public static function detect()
	{
		$directory = path('bundle');

		$extensions = array();

		$items = new fIterator($directory, fIterator::SKIP_DOTS);

		foreach ($items as $item)
		{
			if ($item->isDir())
			{
				if (is_file($item->getRealPath().'/orchestra.json'))
				{
					$extensions[] = json_decode(file_get_contents($item->getRealPath().'/orchestra.json'));
				}
			}
		}

		return $extensions;
	}
}