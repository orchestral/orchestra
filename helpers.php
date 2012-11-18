<?php 

if ( ! function_exists('theme_path'))
{
	/**
	 * Return theme path location
	 *
	 * 
	 * @deprecated      Replaced with locate
	 * @see    locate()
	 * @see    Orchestra\Theme::path()
	 * @param  string   $view
	 * @return string
	 */
	function theme_path($view)
	{
		return locate($view);
	}
}

if ( ! function_exists('locate'))
{
	/**
	 * Return theme path location of a requested view, this would 
	 * allow `Orchestra\Theme` to check for existent of theme file 
	 * associated to the given path before fallback to default view.
	 *
	 * @see    Orchestra\Theme::path()
	 * @param  string   $view
	 * @return string
	 */
	function locate($view)
	{
		return Orchestra\Theme::resolve()->path($view);
	}
}

if ( ! function_exists('memorize'))
{
	/**
	 * Return memory configuration associated to the request
	 *
	 * @see    Orchestra\Core::memory()
	 * @param  string   $key
	 * @param  string   $default
	 * @return mixed
	 */
	function memorize($key, $default = null)
	{
		return Orchestra\Core::memory()->get($key, $default);
	}
}

if ( ! function_exists('handles'))
{
	/**
	 * Return handles configuration for a bundle
	 * 
	 * @param  string   $bundle Bundle name
	 * @return string           URL path
	 */
	function handles($name)
	{
		$handles = '';

		if (strpos($name, '::') !== false)
		{
			list($bundle, $to) = Bundle::parse($name);
		}
		else 
		{
			$bundle = $name;
			$to     = '';
		}

		// In situation where bundle is not registered, it best to assume 
		// that we are handle "application" routing
		if ( ! Bundle::exists($bundle))
		{
			$bundle = DEFAULT_BUNDLE;
			$to     = $bundle;

			// DEFAULT_BUNDLE should handle root path
			$handles = '';
		}
		else
		{
			$handles = Bundle::option($bundle, 'handles');
			$handles = rtrim($handles, '/');
		}
		
		$to = ltrim($to, '/');

		return url($handles.'/'.$to);
	}
}