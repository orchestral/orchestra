<?php

if ( ! function_exists('locate'))
{
	/**
	 * Return theme path location of a requested view, this would allow
	 * `Orchestra\Theme` to check for existent of theme file associated to
	 * the given path before fallback to default view.
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
		$query   = '';

		// split URI and query string.
		if (strpos($name, '?') !== false)
		{
			list($name, $query) = explode('?', $name, 2);
		}

		if (strpos($name, '::') !== false)
		{
			list($bundle, $path) = Bundle::parse($name);
		}
		else
		{
			$bundle = $name;
			$path   = '';
		}

		// In situation where bundle is not registered, it best to assume
		// that we are handle "application" routing
		if ( ! Bundle::exists($bundle))
		{
			$path   = $bundle;
			$bundle = DEFAULT_BUNDLE;

			// DEFAULT_BUNDLE should handle root path
			$handles = '';
		}
		else
		{
			$handles = Bundle::option($bundle, 'handles');
			$handles = rtrim($handles, '/');
		}

		$path = ltrim($path, '/');

		// reappend query string.
		empty($query) or $path .= '?'.$query;

		return url($handles.'/'.$path);
	}
}

if ( ! function_exists('user_localtime'))
{
	/**
	 * Convert given time to user localtime, however if it a guest user 
	 * return based on default timezone.
	 * 
	 * @param  mixed    $datetime
	 * @return DateTime
	 */
	function user_localtime($datetime)
	{
		if ( ! ($datetime instanceof DateTime))
		{
			$datetime = new DateTime(
				$datetime, 
				new DateTimeZone(Config::get('application.timezone', 'UTC'))
			);
		}

		if (Auth::guest()) return $datetime;

		return Auth::user()->localtime($datetime);
	}
}
