<?php namespace Orchestra\Extension;

use \Closure, Laravel\Fluent;

class Pane 
{
	/**
	 * List of registered pane
	 * 
	 * @var array
	 */
	protected static $panes = array();
	
	/**
	 * Register a new pane.
	 *
	 * @static
	 * @access public
	 * @param  string   $id
	 * @param  function $callback
	 * @return void
	 */
	public static function register($id, $callback)
	{
		if ( ! isset(static::$panes[$id]))
		{
			$pane = new Fluent(array(
				'id'      => $id,
				'attr'    => array(),
				'title'   => '',
				'content' => '',
				'html'    => '',
			));

			if ( ! $callback instanceof Closure)
			{
				throw new Exception(__CLASS__.': Excepted a callback');
			}

			call_user_func($callback, $pane);

			static::$panes[$id] = $pane;
		}
	}

	/**
	 * Get all registered pane
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function all()
	{
		return static::$panes;
	}
}