<?php namespace Orchestra;

use Exception, 
	Laravel\Str;

class Resources
{
	/**
	 * The resources registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Register a new resource
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @param  mixed  $controller
	 * @return void
	 */
	public static function make($name, $controller)
	{
		if ( ! is_array($controller))
		{
			$uses       = $controller;
			$controller = array(
				'name'   => Str::title($name),
				'uses'   => $uses,
				'childs' => array(),
			);
		}

		return static::$registrar[$name] = new static($controller);
	}

	/**
	 * Call a resource controller and action.
	 *
	 * @static
	 * @access public
	 * @param  string $name
	 * @param  string $action
	 * @param  array  $arguments
	 * @return Response
	 */
	public static function call($name, $action, $arguments)
	{
		$child = null;

		if (false !== strpos($name, '.'))
		{
			list($name, $child) = explode('.', $name);
		}

		if ( ! isset(static::$registrar[$name])) return false;

		if ( ! is_null($child))
		{
			$controller = isset(static::$registrar[$name]->childs[$child]) ? static::$registrar[$name]->childs[$child] : null;
		}
		else
		{
			$controller = static::$registrar[$name]->uses;
		}

		if (is_null($controller)) return false;

		return Controller::call("{$controller}@{$action}", $arguments);
	}

	/**
	 * Get resource by given name, or create a new one.
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @param  mixed    $controller
	 * @return self
	 */
	public static function of($name, $controller = null)
	{
		if ( ! isset(static::$registrar[$name]))
		{
			return static::make($name, $controller ?: '#');
		}

		return static::$registrar[$name];
	}

	/**
	 * Get all registered resource
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function all()
	{
		return static::$registrar;
	}

	/**
	 * Orchestra\Resources doesn't support a construct method
	 *
	 * @access protected
	 */
	protected function __construct($attributes) 
	{
		$this->attributes = $attributes;
	}

	/**
	 * Resource attributes
	 * 
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Map a child resource attributes
	 * 
	 * @param  string $name
	 * @param  string $uses
	 * @return self
	 */
	public function map($name, $uses)
	{
		$this->attributes['childs'][$name] = $uses;

		return $this;
	}

	/**
	 * Dynamically retrieve the value of an attributes.
	 */
	public function __get($key)
	{
		return $this->attributes[$key] ?: null;
	}

	/**
	 * Dynamically set the value of an attributes.
	 */
	public function __set($key, $value)
	{
		$this->attributes['childs'][$key] = $value;
	}

	/**
	 * Handle dynamic calls to the container to set attributes.
	 */
	public function __call($method, $parameters)
	{
		return $this->attributes[$method] ?: null;
	}
}