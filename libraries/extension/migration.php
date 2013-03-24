<?php namespace Orchestra\Extension;

use RuntimeException, 
	Orchestra\Installer;

abstract class Migration {
	
	/**
	 * Ensure Orchestra Platform is installed before running the migration.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		if ( ! Installer::installed())
		{
			throw new RuntimeException(
				"Orchestra Platform is not installed."
			);
		}
	}
}