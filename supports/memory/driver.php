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
	 * Convert filter to string, this process is required to filter stream 
	 * data return from Postgres where blob type schema would actually use 
	 * BYTEA and convert the string to stream.
	 * 
	 * @access public
	 * @param  mixed    $data
	 * @return string
	 */
	public function stringify($data)
	{
		// check if it's actually a resource, we can directly convert 
		// string without any issue.
		if (is_resource($data))
		{
			// Get the content from stream.
			$hex = stream_get_contents($data);

			// For some reason hex would always start with 'x' and if we
			// don't filter out this char, it would mess up hex to string 
			// conversion.
			if (preg_match('/^x(.*)$/', $hex, $matches)) $hex = $matches[1];

			// Check if it's actually a hex string before trying to convert.
			if (ctype_xdigit($hex))
			{
				$data = '';

				// Convert hex to string.
				for ($i = 0; $i < strlen($hex) - 1; $i += 2)
				{
					$data .= chr(hexdec($hex[$i].$hex[$i+1]));
				}
			}
			else 
			{
				$data = $hex;
			}
		}

		return $data;
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