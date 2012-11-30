<?php namespace Orchestra\Extension\Publisher;

abstract class Driver {

	/**
	 * Get service connection instance.
	 *
	 * @abstract
	 * @access public
	 * @return Object
	 */
	public abstract function connection();

	/**
	 * Connect to the service.
	 *
	 * @abstract
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public abstract function connect($config = array());

	/**
	 * Upload the file.
	 *
	 * @abstract
	 * @access public
	 * @param  string   $name   Extension name
	 * @return bool
	 */
	public abstract function upload($name);

	/**
	 * Verify that the driver is connected to a service.
	 *
	 * @abstract
	 * @access public
	 * @return bool
	 */
	public abstract function connected();
}