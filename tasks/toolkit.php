<?php

class Orchestra_Toolkit_Task {

	/**
	 * Run Orchestra Toolkit
	 *
	 * <code>
	 * 		$ php artisan orchestra::toolkit start
	 * 		$ php artisan orchestra::toolkit init --bundle=mybundle
	 * </code>
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @throws Exception        If provided action is not available
	 * @return void
	 */
	public function run($args)
	{
		$action = array_shift($args);

		if ( ! method_exists($this, "{$action}"))
		{
			throw new Exception("Unable to execute task [{$action}].");
		}

		$this->{$action}($args);
	}

	/**
	 * Return Orchestra Platform version.
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @return string
	 */
	public function version($args)
	{
		echo sprintf("Orchestra Platform version %s", Orchestra::VERSION);
	}

	/**
	 * Run initiate task for Orchestra Toolkit, this would add the definition
	 * file as well as start file for Orchestra.
	 *
	 * <code>
	 * 		$ php artisan orchestra::toolkit:init
	 * 		$ php artisan orchestra::toolkit:init --bundle=mybundle
	 * </code>
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @return void
	 */
	public function init($args)
	{
		$this->definition($args);
		$this->start($args);
	}

	/**
	 * Add definition file to bundle.
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @throws Exception        If file already exists.
	 * @return void
	 */
	public function definition($args)
	{
		list($bundle, $path) = $this->path_resolver();

		if (File::exists($file = $path.'orchestra.json'))
		{
			throw new Exception(
				"File [{$bundle}::orchestra.json] already exists, unable to overwrite."
			);
		}

		File::copy(Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra.json', $file);
		echo "File [{$bundle}::orchestra.json] is created.\r\n";
	}

	/**
	 * Add start file to bundle.
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @throws Exception        If file already exists.
	 * @return void
	 */
	public function start($args)
	{
		list($bundle, $path) = $this->path_resolver();

		if (File::exists($file = $path.'orchestra.php'))
		{
			throw new Exception(
				"File [{$bundle}::orchestra.php] already exists, unable to overwrite."
			);
		}

		File::copy(Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra.php', $file);

		echo "File [{$bundle}::orchestra.php] is created.\r\n";
	}

	/**
	 * Add installer file to bundle.
	 *
	 * @access public
	 * @param  array    $args   Arguments passed by CLI
	 * @throws Exception        If file already exists or bundle is not the
	 *                          DEFAULT_BUNDLE.
	 * @return void
	 */
	public function installer($args)
	{
		list($bundle, $path) = $this->path_resolver();

		if ($bundle !== DEFAULT_BUNDLE)
		{
			throw new Exception("This task can only be used with DEFAULT_BUNDLE.");
		}

		if (File::exists($file = $path.'orchestra/installer.php'))
		{
			throw new Exception(
				"File [{$bundle}::orchestra/installer.php] already exists, unable to overwrite."
			);
		}

		File::mkdir($path.'orchestra');
		File::copy(
			Bundle::path('orchestra').'tasks'.DS.'stubs'.DS.'orchestra'.DS.'installer.php',
			$file
		);

		echo "File [{$bundle}::orchestra/installer.php] is created.\r\n";
	}

	/**
	 * Resolve bundle path, for unregistered bundle it would not be accessible
	 * from Bundle::path() helper, in this case we need to manually figure
	 * out the valid path using path() helpers.
	 *
	 * @access private
	 * @return string
	 */
	private function path_resolver()
	{
		$bundle = get_cli_option('bundle') ?: DEFAULT_BUNDLE;

		if (Bundle::exists($bundle)) return array($bundle, Bundle::path($bundle));

		return array($bundle, path('bundle').$bundle.DS);
	}
}
