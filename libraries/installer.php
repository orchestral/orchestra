<?php namespace Orchestra;

use \Config, \DB, Exception;

class Installer {

	/**
	 * Installation status
	 *
	 * @static
	 * @access public
	 * @var    boolean
	 */
	public static $status = false;

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

	/**
	 * Check database connection
	 *
	 * @static
	 * @access public
	 * @return bool     return true if database successfully connected
	 */
	public static function check_database()
	{
		try
		{
			DB::connection(Config::get('database.default'))->pdo;
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}
