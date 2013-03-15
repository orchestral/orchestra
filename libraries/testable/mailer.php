<?php namespace Orchestra\Testable;

class Mailer {

	protected $was_sent = false;

	/**
	 * Get a Mock Mailer driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function instance($driver = null)
	{
		return new static(array());
	}

	/**
	 * Register the Swift Mailer message and transport instances.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config) {}
	
	/**
	 * Assume that we going to accept everything, for now.
	 */
	public function __call($method, $parameters) 
	{
		return $this;
	}

	public function send() 
	{
		$this->was_sent = true;
	}

	public function was_sent($email)
	{
		return $this->was_sent;
	}
}