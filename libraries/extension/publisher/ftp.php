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

		}
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
		$base_pwd    = $this->connection->pwd();
		$public_path = path('public');

		// This set of preg_match would filter ftp user is not accessing 
		// the pull path, in most shared hosting ftp user would only gain
		// access to it's /home/username directory
		if (preg_match('/^\/(home)\/([a-zA-Z0-9]+)\/(.*)$/', $public_path, $matches))
		{
			$public_path = DS.ltrim($matches[3], DS);
		}

		$public_path = rtrim($public_path, DS).DS;

		$this->connection->chmod($public_path.'bundles', 0777);

		Extension::activate($name);

		$this->connection->chmod($public_path.'bundles', 0755);

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