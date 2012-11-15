<?php namespace Orchestra\Widget;

use \Config, 
	Laravel\Fluent;

abstract class Driver {

	/**
	 * Transerve instance
	 *
	 * @access  protected
	 * @var     Widget\Traverse
	 */
	protected $traverse = null;

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
		$_config        = Config::get('orchestra::widget.'.$this->type, array());
		$this->name     = $name;
		$this->config   = array_merge($config, $_config);
		$this->traverse = new Traverse($this->config);
	}

	/**
	 * Shortcut to render()
	 *
	 * @access  public
	 * @see     self::render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Add an item to current widget.
	 *
	 * @access public
	 * @param  string   $id
	 * @param  string   $location
	 * @return mixed
	 */
	public abstract function add($id, $location = 'parent');

	/**
	 * Render widget as a view
	 *
	 * @access  public
	 * @return  string
	 */
	public abstract function render();

	/**
	 * Get all items
	 */
	public function get()
	{
		return $this->traverse->get();
	}

	/**
	 * Magic method to get all items
	 * 
	 * @param  string   $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if ($key === 'items') return $this->get();
	}
}