<?php namespace Orchestra;

use \Bundle, \URL;

class Theme
{
	/**
	 * Active theme folder name
	 * 
	 * @var string
	 */
	protected static $name = null;

	/**
	 * Themes aliases, allowing similar view to be mapped without having to duplicate the 
	 * physical file.
	 *
	 * @var array 
	 */
	protected static $aliases = array();

	/**
	 * Filesystem path of Theme
	 *  
	 * @var string
	 */
	protected static $path = null;

	/**
	 * URL path of Theme
	 * 
	 * @var string
	 */
	protected static $url  = null;

	/**
	 * Start Theme Engine, this should be called from Orchestra\Core::start() or whenever we need to 
	 * overwrite current active theme per request.
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public static function start($name = 'default')
	{
		if (is_null(static::$path))
		{
			static::$path = path('public').'themes';
			static::$url  = rtrim(URL::base(), '/').'/themes';
		} 

		if (is_dir(static::$path.DS.$name))
		{
			static::$name = $name;
		}
	}

	/**
	 * Path helper for Theme
	 *
	 * @static
	 * @access public
	 * @param  string   $file
	 * @return string
	 */
	public static function path($file = '')
	{
		return static::parse($file, false);
	}

	/**
	 * URL helper for Theme
	 *
	 * @static
	 * @access public
	 * @param  string   $url
	 * @return string
	 */
	public static function to($url = '')
	{
		return static::$url.'/'.static::$name.'/'.$url;
	}

	/**
	 * Map theme aliases, to allow a similar views to be map together
	 * without make multiple file.
	 *
	 * <code>
	 *     Orchestra\Theme::map(array(
	 *         'bundle::view.page'   => 'bundle2::view.page',
	 *         'bundle::view.header' => 'path: /path/to/view.blade.php',
	 *     ));
	 * </code>
	 *
	 * @static
	 * @access public	
	 * @param  array    $aliases
	 * @return void         
	 */
	public static function map($aliases)
	{
		foreach ((array) $aliases as $alias => $file)
		{
			if ( ! is_numeric($alias))
			{
				static::$aliases[$alias] = static::parse($file);
			}
		}
	}

	/**
	 * Parse normal View to use Theme
	 *
	 * @static
	 * @access public
	 * @param  string   $file
	 * @return string
	 */
	public static function parse($file, $use_bundle = true)
	{
		// Return the file if it's already using full path to 
		// avoid recursive request.
		if (starts_with('path: ', $file)) return $file;

		// Check theme aliases if we already have registered aliases
		if (isset(static::$aliases[$file])) return static::$aliases[$file];

		if ( ! is_null(static::$name)) 
		{
			if (strpos($file, '::') !== false)
			{
				list($bundle, $view) = Bundle::parse($file);
			}
			else 
			{
				$bundle = null;
				$view   = $file;
			}

			// In situation where bundle is not registered, it best to assume 
			// that we are handle "application" routing
			if ( ! Bundle::exists($bundle))
			{
				$bundle = DEFAULT_BUNDLE;
			}

			$directory = static::$path.DS.static::$name.DS;

			if ($use_bundle)
			{
				$directory .= $bundle.DS;
			}

			$view = str_replace('.', '/', $view);

			// Views may have either the default PHP file extension or the "Blade"
			// extension, so we will need to check for both in the view path
			// and return the first one we find for the given view.
			if (file_exists($path = $directory.$view.EXT))
			{
				return 'path: '.$path;
			}
			elseif (file_exists($path = $directory.$view.BLADE_EXT))
			{
				return 'path: '.$path;
			}
		}

		return $file;
	}
}