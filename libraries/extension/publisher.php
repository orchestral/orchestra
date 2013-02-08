<?php namespace Orchestra\Extension;

use \Closure,
	\Exception,
	\InvalidArgumentException,
	\Redirect,
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

		return new Publisher\FTP;
	}

	/**
	 * Register a third-party publisher driver.
	 *
	 * @static
	 * @access public
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	/**
	 * Add a process to be queue.
	 *
	 * @static
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
	 * @static
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
	 * @static
	 * @access public
	 * @param  Messages $msg 
	 * @return Messages
	 */
	public static function execute(& $msg)
	{
		if ( ! $msg instanceof Messages)
		{
			throw new InvalidArgumentException(
				'Invalid argument, expect to be instanceof Orchestra\Messages'
			);
		}

		$queues = static::queued();

		foreach ($queues as $key => $queue)
		{
			try
			{
				static::upload($queue);
				
				$msg->add('success', __('orchestra::response.extensions.activate', array(
					'name' => $queue,
				)));

				unset($queues[$key]);
			}
			catch (Exception $e)
			{
				// this could be anything.
				$msg->add('error', $e->getMessage());
			}
		}

		Session::put('orchestra.publisher.queue', $queues);

		return $msg;
	}

	/**
	 * Magic Method for calling the methods on the default cache driver.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}
}