<?php namespace Orchestra\Support;

use \Closure,
	\InvalidArgumentException;

abstract class Decorator {

	/**
	 * Create a new Decorator instance
	 *
	 * @static
	 * @access  public
	 * @param   Closure     $callback
	 * @return  Decorator
	 */
	public static function make(Closure $callback)
	{
		return new static($callback);
	}
	
	/**
	 * Name of decorator.
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Grid instance.
	 *
	 * @var object
	 */
	protected $grid = null;

	/**
	 * Create a new Decorator instance.
	 * 			
	 * @access public
	 * @param  Closure      $callback
	 * @return void	 
	 */
	abstract public function __construct(Closure $callback);

	/**
	 * Extend decoration. 
	 *
	 * @access public
	 * @param  Closure $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// Run the table designer.
		call_user_func($callback, $this->grid);
	}

	/**
	 * Magic method to get Grid instance.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('grid', 'name'))) 
		{
			throw new InvalidArgumentException(
				"Unable to get property [{$key}]."
			);
		}
		
		return $this->{$key};
	}

	/**
	 * An alias to render()
	 *
	 * @access  public
	 * @see     render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Render the decoration.
	 *
	 * @abstract
	 * @access  public
	 * @return  string
	 */
	abstract public function render();
}
