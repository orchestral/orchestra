<?php namespace Orchestra;

use \Bundle, 
	\IoC, 
	\URL;

class Theme {

	/**
	 * All of the instantiated theme containers.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Start Theme when Orchestra Platform is loaded.
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function start()
	{
		$memory = Core::memory();

		// Define IoC for Theme.
		IoC::singleton('orchestra.theme: backend', function() use ($memory)
		{
			$theme = $memory->get('site.theme.backend', 'default');

			return Theme::container('backend', $theme);
		});

		IoC::singleton('orchestra.theme: frontend', function() use ($memory)
		{
			$theme = $memory->get('site.theme.frontend', 'default');

			return Theme::container('frontend', $theme);
		});
	}

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
		return call_user_func_array(array(static::resolve(), $method), $parameters);
	}
}