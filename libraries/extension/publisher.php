<?php namespace Orchestra\Extension;

use \Redirect,
	\Session, 
	Orchestra\Core,
	Orchestra\Messages;

class Publisher {

	/**
	 * The currently active publisher drivers.
	 *
	 * @var array
	 */
	public static $drivers = array();

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Get an publisher driver instance.
	 *
	 * @static
	 * @access public
	 * @param  string  $driver
	 * @return Publisher\Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver))
		{
			$driver = Core::memory()->get('orchestra.publisher.driver', 'ftp');
		}

		if ( ! isset(static::$drivers[$driver]))
		{
			static::$drivers[$driver] = static::factory($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Create a new publisher driver instance.
	 * 
	 * @static
	 * @access protected
	 * @param  string   $driver
	 * @return Publisher\Driver
	 */
	protected static function factory($driver)
	{
		if (isset(static::$registrar[$driver]))
		{
			$resolver = static::$registrar[$driver];

			return $resolver();
		}

		switch ($driver)
		{
			case 's3' :
				return new Publisher\S3;
			case 'ftp' :
				// pass through.
			default :
				return new Publisher\FTP;
		}
	}

	/**
	 * Add a process to be queue.
	 *
	 * @access public
	 * @param  string   $queue
	 * @return bool
	 */
	public static function queue($queue)
	{
		$queue = static::queued() + (array) $queue;

		Session::put('orchestra.publisher.queue', $queue);

		return true;
	}

	/**
	 * Get a current queue.
	 *
	 * @access public
	 * @return array
	 */
	public static function queued()
	{
		return Session::get('orchestra.publisher.queue', array());
	}

	/**
	 * Execute the queue.
	 * 
	 * @return void
	 */
	public static function execute()
	{
		$queue = static::queued();
		$m     = new Messages;

		foreach ($queue as $name)
		{
			try
			{
				static::upload($name);
				
				$m->add('success', __('orchestra::response.extensions.activate', array(
					'name' => $name,
				)));
			}
			catch (Exception $e)
			{
				// this could be anything.
				$m->add('error', $e->getMessage());
			}
		}

		Session::forget('orchestra.publisher.queue');

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}

	/**
	 * Magic Method for calling the methods on the default cache driver.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}
}