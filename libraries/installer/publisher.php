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
		$directory = path('public').'bundles'.DS;
		$run_task  = with(new Publisher\Directory)->flush($directory);

		// If it's still unable to be writable, return false.
		if ( ! is_writable($directory)) return false;

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

	/**
	 * 
	 */
}