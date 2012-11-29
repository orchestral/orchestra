<?php namespace Orchestra\Installer;

use \Bundle,
	\File,
	\IoC;

class Publisher {

	/**
	 * Manually run `php artisan bundle:publish` on the web if we have the 
	 * access to write to directory.
	 *
	 * @access public
	 * @return void
	 */
	public function publish()
	{
		$asset_directory = path('public').'bundles/';

		// This set of code check whether the application has the right set 
		// of directory configuration in order to be updated through web. If 
		// such issue exist it would be best to flush the directory and let 
		// web server file/group permission to create the folder.
		if (is_dir($asset_directory) and is_writable($asset_directory)) return true;
		
		File::delete($asset_directory);
		File::mkdir($asset_directory, 0755);

		if ( ! is_writable($asset_directory)) return false;

		foreach (array_keys(Bundle::$bundles) as $bundle)
		{
			IoC::resolve('task: orchestra.publisher', array($bundle));
		}

		return true;
	}
}