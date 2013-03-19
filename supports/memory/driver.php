<?php namespace Orchestra\Support\Memory;

abstract class Driver {
	
	/**
	 * Memory name
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $name = null;

	/**
	 * Memory configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array();

	/**
	 * Collection of key-value pair of either configuration or data
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $data = array();

	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage;

	/**
	 * Construct an instance.
	 *
	 * @access  public
	 * @param   string  $storage    set storage configuration (default to 'runtime').
	 */
	public function __construct($name = 'default', $config = array()) 
	{
		$this->name   = $name;
		$this->config = is_array($config) ? $config : array(); 

		$this->initiate();
	}

	/**
	 * Get value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to search.
	 * @param   mixed   $default    Default value if key doesn't exist.
	 * @return  mixed
	 */
	public function get($key = null, $default = null)
	{
		$value = array_get($this->data, $key, null);

		if ( ! is_null($value)) return $value;

		return value($default);
	}

	/**
	 * Set a value from a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to add the value.
	 * @param   mixed   $value      The value.
	 * @return  mixed
	 */
	public function put($key, $value = '')
	{
		$value = value($value);
		array_set($this->data, $key, $value);

		return $value;
	}

	/**
	 * Delete value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to delete.
	 * @return  bool
	 */
	public function forget($key = null)
	{
		return array_forget($this->data, $key);
	}

	/**
	 * Initialize method
	 *
	 * @abstract
	 * @access  public
	 * @return  void
	 */
	public abstract function initiate();
	
	/**
	 * Shutdown method
	 *
	 * @abstract
	 * @access  public
	 * @return  void
	 */
	public abstract function shutdown();
}