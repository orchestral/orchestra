<?php namespace Orchestra\Extension;

use \Closure, Laravel\Fluent;

class Pane 
{
	protected static $panes = array();
	
	public static function make($id, $callback)
	{
		if ( ! isset(static::$panes[$id]))
		{
			$pane = new Fluent(array(
				'id'      => $id,
				'attr'    => array(),
				'title'   => '',
				'content' => '',
			));

			if ( ! $callback instanceof Closure)
			{
				throw new Exception(__CLASS__.': Excepted a callback');
			}

			call_user_func($callback, $pane);

			static::$panes[$id] = $pane;
		}
	}

	public static function get()
	{
		return static::$panes;
	}
}