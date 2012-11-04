<?php namespace Orchestra;

use \Bundle, \IoC, \URL;

class Theme
{
	/**
	 * All of the instantiated theme containers.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Shorthand to resolve current active Theme.
	 *
	 * @static
	 * @access public
	 * @return self
	 */
	public static function resolve()
	{
		return IoC::resolve('orchestra.theme: '.View::$theme);
	}

	/**
	 * Get an theme container instance.
	 *
	 * <code>
	 *		// Get the default asset container
	 *		$container = Orchestra\Theme::container();
	 *
	 *		// Get a named asset container
	 *		$container = Orchestra\Theme::container('footer');
	 * </code>
	 *
	 * @static
	 * @access public
	 * @param  string            $container
	 * @return Theme\Container
	 */
	public static function container($container = 'frontend', $name = 'default')
	{
		if ( ! isset(static::$containers[$container]))
		{
			static::$containers[$container] = new Theme\Container($name);
		}

		return static::$containers[$container];
	}
	
	/**
	 * Magic Method for calling methods on the default container.
	 *
	 * <code>
	 *		// Call the "path" method on the default container
	 *		echo Theme::map(array(
	 *			'orchestra::layout.main' => 'backend::layout.main',
	 *		));
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}
}