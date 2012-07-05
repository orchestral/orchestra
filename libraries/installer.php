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

	public static function make() {}

	public function __construct() {}

	public function check($checklist)
	{
		switch ($checklist)
		{
			case 'database-connection' :
				return DBUtil::database_exists();
				break;
		}
	}

	public function migrate()
	{
		
	}
}