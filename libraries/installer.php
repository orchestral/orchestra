<?php namespace Orchestra;

class Installer
{
	/**
	 * Installation status
	 *
	 * @static
	 * @access public
	 * @var    boolean
	 */
	public static $status = true;

	/**
	 * Return whether Orchestra is installed
	 *
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function installed()
	{
		return static::$status;
	}
}