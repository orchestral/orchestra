<?php namespace Orchestra\Theme;

use \Bundle,
	\URL;

class Container {

	/**
	 * Active theme folder name
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Themes aliases, allowing similar view to be mapped without having to
	 * duplicate the physical file.
	 *
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * Theme view path cache, avoid extensive call to resolve path for theme.
	 *
	 * @var array
	 */
	protected $cache_path = array();

	/**
	 * Filesystem path of Theme
	 *
	 * @var string
	 */
	protected $path = null;

	/**
	 * URL path of Theme
	 *
	 * @var string
	 */
	protected $url  = null;

	/**
	 * Relative URL path of Theme
	 *
	 * @var string
	 */
	protected $relative_url  = null;

	/**
	 * Start Theme Engine, this should be called from Orchestra\Core::start()
	 * or whenever we need to overwrite current active theme per request.
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public function __construct($name = 'default')
	{
		if (is_null($this->path))
		{
			$this->path         = path('public').'themes';
			$this->url          = rtrim(URL::base(), '/').'/themes';
			$this->relative_url = str_replace(URL::base(), '/', $this->url);
		}

		if (is_dir($this->path.DS.$name))
		{
			$this->name = $name;
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
	public function path($file = '')
	{
		return $this->parse($file);
	}

	/**
	 * URL helper for Theme
	 *
	 * @static
	 * @access public
	 * @param  string   $url
	 * @return string
	 */
	public function to($url = '')
	{
		return $this->url.'/'.$this->name.'/'.$url;
	}

	/**
	 * Relative URL helper for Theme
	 *
	 * @static
	 * @access public
	 * @param  string   $url
	 * @return string
	 */
	public function to_asset($url = '')
	{
		return $this->relative_url.'/'.$this->name.'/'.$url;
	}

	/**
	 * Map theme aliases, to allow a similar views to be map together without
	 * make multiple file.
	 *
	 * <code>
	 *     $theme->map(array(
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
	public function map($aliases)
	{
		foreach ((array) $aliases as $alias => $file)
		{
			if ( ! is_numeric($alias))
			{
				$this->aliases[$alias] = $this->parse($file);
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
	public function parse($file, $use_bundle = true)
	{
		$name = $file;

		// Return the file if it's already using full path to avoid
		// recursive request.
		if (starts_with('path: ', $file)) return $file;

		// Check theme aliases if we already have registered aliases
		if (isset($this->aliases[$name])) return $this->aliases[$file];

		// Check from theme path cache.
		if (isset($this->cache_path[$name])) return $this->cache_path[$file];

		if ( ! is_null($this->name))
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
			// that we are handle "application" routing.
			if ( ! Bundle::exists($bundle))
			{
				$bundle = DEFAULT_BUNDLE;
			}

			$directory = $this->path.DS.$this->name.DS;

			if ($use_bundle and $bundle !== DEFAULT_BUNDLE)
			{
				$directory .= 'bundles'.DS.$bundle.DS;
			}

			$view = str_replace('.', '/', $view);

			// Views may have either the default PHP file extension or the
			// "Blade" extension, so we will need to check for both in the
			// view path and return the first one we find for the given view.
			if (file_exists($path = $directory.$view.EXT))
			{
				$file = 'path: '.$path;
			}
			elseif (file_exists($path = $directory.$view.BLADE_EXT))
			{
				$file = 'path: '.$path;
			}
		}

		return $this->cache_path[$name] = $file;
	}
}