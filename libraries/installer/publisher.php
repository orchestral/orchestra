<?php namespace Orchestra\Installer;

use \RuntimeException,
	\Bundle,
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
		$run_task        = false;
		$asset_directory = path('public').'bundles/';

		// This set of code check whether the application has the right set 
		// of directory configuration in order to be updated through web. If 
		// such issue exist it would be best to flush the directory and let 
		// web server file/group permission to create the folder.
		if ( ! is_dir($asset_directory) or ! is_writable($asset_directory))
		{
			// only remove directory if it's exist.
			is_dir($asset_directory) or File::rmdir($asset_directory);

			// try mkdir, in certain scenario this would be possible if the 
			// public folder ownership is set correctly.
			if ( ! @mkdir($asset_directory, 0755, true))
			{
				throw new RuntimeException(
					"Unable to create directory [{$asset_directory}] due to permission issue."
				);
			}

			$run_task = true;
		}

		// If it's still unable to be writable, return false.
		if ( ! is_writable($asset_directory)) return false;

		// Avoid web server to run extensive file migration process unless 
		// necessary (if the folder is remove and re-created). 
		if (true === $run_task)
		{
			foreach (array_keys(Bundle::$bundles) as $bundle)
			{
				IoC::resolve('task: orchestra.publisher', array($bundle));
			}
		}

		return true;
	}
}