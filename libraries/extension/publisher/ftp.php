<?php namespace Orchestra\Extension\Publisher;

use \RuntimeException,
	\IoC,
	\Session,
	Hybrid\FTP as F,
	Orchestra\Extension;

class FTP extends Driver {

	/**
	 * FTP Connection instance.
	 * 
	 * @var Hybrid\FTP
	 */
	public $connection = null;

	/**
	 * Construct a new FTP instance.
	 */
	public function __construct()
	{
		// If FTP credential is stored in the session, we should reuse it 
		// and connect to FTP server straight away.
		$config = Session::get('orchestra.ftp', array());

		if ( ! empty($config)) $this->connect($config);
	}

	/**
	 * Get service connection instance.
	 *
	 * @access public
	 * @return Hybrid\FTP
	 */
	public function connection()
	{
		return $this->connection;
	}

	/**
	 * Connect to the service.
	 *
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public function connect($config = array())
	{
		try
		{
			$this->connection = F::make($config);
			$this->connection->connect();
		}
		catch (RuntimeException $e)
		{
			// Connection might failed, but there nothing really to report.
		}
	}

	/**
	 * Check chmod for a file/directory recursively.
	 *
	 * @access private
	 * @param  string   $path
	 * @param  int      $mode
	 * @return bool
	 */
	private function recursive_chmod($path, $mode = 0755)
	{
		$this->connection->chmod($path, $mode);

		try
		{
			$lists = $this->connection->ls($path);

			// this is to check if return value is just a single file, 
			// avoiding infinite loop when we reach a file.
			if ($lists === array($path)) return true;
		}
		catch (Hybrid\RuntimeException $e)
		{
			return true;
		}

		foreach ($lists as $dir)
		{
			// Not a file or folder, ignore it.
			if (substr($dir, -3) === '/..' or substr($dir, -2) === '/.') continue;
			
			$this->recursive_chmod($dir, $mode);
		}

		return true;
	}


	/**
	 * Upload the file.
	 *
	 * @access public
	 * @param  string   $name   Extension name
	 * @return bool
	 */
	public function upload($name)
	{
		$base_pwd = $this->connection->pwd();
		$public   = path('public');

		// This set of preg_match would filter ftp' user is not accessing 
		// exact path as path('public'), in most shared hosting ftp' user 
		// would only gain access to it's /home/username directory.
		if (preg_match('/^\/(home)\/([a-zA-Z0-9]+)\/(.*)$/', $public, $matches))
		{
			$public = DS.ltrim($matches[3], DS);
		}

		// Start chmod from public/bundles directory, if the extension folder
		// is yet to be created, it would be created and own by the web server
		// (Apache or Nginx). If otherwise, we would then emulate chmod -Rf
		$public = rtrim($public, DS).DS;
		$path   = $public.'bundles'.DS;

		// If the extension directory exist, we should start chmod from the
		// folder instead.
		if (is_dir($public.'bundles'.DS.$name.DS)) $path = $path.$name.DS;

		try 
		{
			$this->recursive_chmod($path, 0777);
		}
		catch (Hybrid\RuntimeException $e)
		{
			// We found an exception with FTP, but it would be hard to say 
			// extension can't be activated, let's try activating the 
			// extension and if it failed, we should actually catching 
			// those exception instead.
		}

		Extension::activate($name);
		
		// Revert chmod back to original state.
		$this->recursive_chmod($path, 0755);
		
		return true;
	}

	/**
	 * Verify that FTP driver is connected to a service.
	 * 
	 * @access public
	 * @return bool
	 */
	public function connected()
	{
		if (is_null($this->connection)) return false;

		return $this->connection->connected();
	}
}