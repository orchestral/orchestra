<?php namespace Orchestra;

use \Bundle,
	\Str, 
	Laravel\Routing\Controller as Laravel_Controller;

abstract class Controller extends Laravel_Controller
{
	/**
	 * Format a bundle and controller identifier into the controller's class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $controller
	 * @return string
	 */
	protected static function format($bundle, $controller)
	{
		return Str::classify($bundle).'\\'.Str::classify($controller).'_Controller';
	}
}