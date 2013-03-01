<?php namespace Orchestra\Widget;

use \Config,
	\InvalidArgumentException,
	Orchestra\Support\Fluent;

abstract class Driver {

	/**
	 * Nesty instance
	 *
	 * @access  protected
	 * @var     Widget\Nesty
	 */
	protected $nesty = null;

	/**
	 * Name of this instance
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $name = null;

	/**
	 * Configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array();

	/**
	 * Type
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $type = null;

	/**
	 * Construct a new instance
	 *
	 * @access  public
	 * @param   string  $name
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct($name, $config = array())
	{
		$_config      = Config::get('orchestra::widget.'.$this->type, $this->config);
		$this->name   = $name;
		$this->config = array_merge($config, $_config);
		$this->nesty  = new Nesty($this->config);
	}

	/**
	 * Add an item to current widget.
	 *
	 * @access public
	 * @param  string   $id
	 * @param  mixed    $location
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public abstract function add($id, $location = 'parent', $callback = null);

	/**
	 * Get all items
	 */
	public function get()
	{
		return $this->nesty->get();
	}

	/**
	 * Magic method to get all items
	 *
	 * @param  string   $key
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function __get($key)
	{
		if ($key !== 'items') 
		{
			throw new InvalidArgumentException("Access to [{$key}] is not allowed.");
		}

		return $this->get();
	}
}
