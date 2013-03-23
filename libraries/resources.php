<?php namespace Orchestra;

use \Closure,
	\Exception,
	\InvalidArgumentException,
	Laravel\Redirect,
	Laravel\Response,
	\Str;

class Resources {

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
	 * @param  string   $name
	 * @param  mixed    $controller
	 * @return Resources
	 */
	public static function make($name, $controller)
	{
		$schema = array(
			'name'    => '',
			'uses'    => '',
			'childs'  => array(),
			'visible' => true,
		);

		if ( ! is_array($controller))
		{
			$uses       = $controller;
			$controller = array(
				'name'    => Str::title($name),
				'uses'    => $uses,
			);
		}

		$controller       = array_merge($schema, $controller); 
		$controller['id'] = $name;

		if (empty($controller['name']) or empty($controller['uses']))
		{
			throw new InvalidArgumentException("Required `name` and `uses` are missing.");
		}

		return static::$registrar[$name] = new static($controller);
	}

	/**
	 * Call a resource controller and action.
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @param  string   $action
	 * @param  array    $arguments
	 * @return Response
	 */
	public static function call($name, $action, $arguments)
	{
		$child = null;

		if (false !== strpos($name, '.'))
		{
			list($name, $child) = explode('.', $name);
		}

		// If resources is not set, we should return false.
		if ( ! isset(static::$registrar[$name])) return false;

		if ( ! is_null($child))
		{
			$controller = isset(static::$registrar[$name]->childs[$child]) ?
				static::$registrar[$name]->childs[$child] : null;
		}
		else
		{
			$controller = static::$registrar[$name]->uses;
		}

		// This would cater request to valid resource but pointing to an
		// invalid child. We should show a 404 response to the user on this
		// case.
		if (is_null($controller)) return false;

		return Controller::call("{$controller}@{$action}", $arguments);
	}

	/**
	 * Handle response from resources.
	 *
	 * @static
	 * @access public
	 * @param  mixed    $content
	 * @param  Closure  $default
	 * @return Response
	 */
	public static function response($content, Closure $default = null)
	{
		switch (true)
		{
			case ( ! $content) :
				return Response::error('404');
			
			case ($content instanceof Redirect) :
				return $content;
		
			case ($content instanceof Response) :

				$status_code = $content->foundation->getStatusCode();

				if ( ! $content->foundation->isSuccessful())
				{
					return Response::error($status_code);
				}

				break;
			default :
				// nothing to do here.
		}

		if ($default instanceof Closure)
		{
			$content = call_user_func($default, $content);
		}

		return $content;
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
	 * Reserved keywords
	 *
	 * @var array
	 */
	protected $reserved_keywords = array('visible');

	/**
	 * Map a child resource attributes
	 *
	 * @access public
	 * @param  string $name
	 * @param  string $uses
	 * @return self
	 */
	public function map($name, $uses)
	{
		if (in_array($name, $this->reserved_keywords))
		{
			throw new InvalidArgumentException("Unable to use reserved keyword [{$name}].");
		}

		$this->attributes['childs'][$name] = $uses;

		return $this;
	}

	/**
	 * Set visibility state based on parameter
	 *
	 * @access public
	 * @param  bool     $value
	 * @return self
	 */
	public function visibility($value)
	{
		$this->attributes['visible'] = $value;

		return $this;
	}

	/**
	 * Set visibility state to show
	 * 
	 * @access public
	 * @return self
	 */
	public function show()
	{
		$this->attributes['visible'] = true;

		return $this;
	}

	/**
	 * Set visibility state to hidden
	 *
	 * @access public
	 * @return self
	 */
	public function hide()
	{
		$this->attributes['visible'] = false;
		
		return $this;
	}

	/**
	 * Dynamically retrieve the value of an attributes.
	 */
	public function __get($key)
	{
		return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
	}

	/**
	 * Dynamically set the value of an attributes.
	 */
	public function __set($key, $value)
	{
		$this->map($key, $value);
	}

	/**
	 * Handle dynamic calls to the container to set attributes.
	 */
	public function __call($method, $parameters)
	{
		if( ! empty($parameters))
		{
			throw new InvalidArgumentException("Unexpected parameters.");
		}

		return $this->attributes[$method] ?: null;
	}
}
