<?php namespace Orchestra\Installer\Publisher;

use \RuntimeException,
	\File;

class Directory {
	
	/**
	 * Flush asset directory configuration by trying to delete and mkdir 
	 * again.
	 *
	 * @access public
	 * @param  string   $directory
	 * @return bool
	 * @throws RuntimeException
	 */
	public function flush($directory)
	{
		// This set of code check whether the application has the right set 
		// of directory configuration in order to be updated through web. If 
		// such issue exist it would be best to flush the directory and let 
		// web server file/group permission to create the folder.
		if ( ! is_dir($directory) or ! is_writable($directory))
		{
			// only remove directory if it's exist.
			is_dir($directory) or File::rmdir($directory);

			$this->create($directory);
		}

		return true;
	}

	/**
	 * Create the directory with chmod 755
	 *
	 * @access public
	 * @param  string   $directory
	 * @param  int      $chmod
	 * @return bool
	 * @throws RuntimeException
	 */
	public function create($directory, $chmod = 0755)
	{
		// try mkdir, in certain scenario this would be possible if the 
		// public folder ownership is set correctly.
		if ( ! @mkdir($directory, $chmod, true))
		{
			throw new RuntimeException(
				"Unable to create directory [{$directory}] due to permission issue."
			);
		}

		return true;
	}
}