<?php

class Orchestra_Toolkit_Task {
	
	public function run($args)
	{
		$action = array_shift($args);

		if ( ! method_exists($this, "{$action}"))
		{
			throw new Exception("Unable to execute task [{$action}].");
		}

		$this->{$action}($args);
	}
	
	public function definition($args)
	{
		$bundle = get_cli_option('bundle') ?: DEFAULT_BUNDLE;
		$path   = Bundle::path($bundle);

		if (File::exists($file = $path.'orchestra.json'))
		{
			echo "File [{$bundle}::orchestra.json] already exists, unable to overwrite.\r\n";
		}
		else
		{
			File::copy(Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra.json', $file);
			echo "File [{$bundle}::orchestra.json] is created.\r\n";
		}

		if (File::exists($file = $path.'orchestra.php'))
		{
			echo "File [{$bundle}::orchestra.php] already exists, unable to overwrite.\r\n";
		}
		else
		{
			File::copy(Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra.php', $file);
			echo "File [{$bundle}::orchestra.php] is created.\r\n";
		}
	}

	public function installer($args)
	{
		$bundle = get_cli_option('bundle') ?: DEFAULT_BUNDLE;
		$path   = Bundle::path($bundle);

		if ($bundle !== DEFAULT_BUNDLE)
		{
			throw new Exception("This task can only be used with DEFAULT_BUNDLE.");
		}

		if (File::exists($file = $path.'orchestra/installer.php'))
		{
			echo "File [{$bundle}::orchestra/installer.php] already exists, unable to overwrite.\r\n";
		}
		else
		{
			File::mkdir($path.'orchestra');
			File::copy(Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra'.DS.'installer.php', $file);

			echo "File [{$bundle}::orchestra/installer.php] is created.\r\n";
		}
	}
}