<?php namespace Orchestra\Widget;

use \Config, Laravel\Fluent;

abstract class Driver
{
	/**
	 * List of items
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $items = array();

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
		$_config      = Config::get('orchestra::widget.'.$this->type, array());
		$this->name   = $name;
		$this->config = array_merge($config, $_config);
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
	 * Add item before reference $before
	 *
	 * @access protected
	 * @param  string   $id
	 * @param  string   $before
	 * @return Fluent
	 */
	protected function add_before($id, $before)
	{
		$items = array();
		$item  = new Fluent(array(
			'id'     => $id,
			'childs' => array()
		));

		$keys     = array_keys($this->items);
		$position = array_search($before, $keys);

		if (is_null($position)) return $this->add_parent($id);

		if ($position > 0) $position--;

		foreach ($keys as $key => $fluent)
		{
			if ($key === $position)
			{
				$items[$id] = $item;
			}

			$items[$fluent] = $this->items[$fluent];
		}

		$this->items = $items;

		return $item;
	}

	/**
	 * Add item after reference $after
	 *
	 * @access protected
	 * @param  string   $id
	 * @param  string   $after
	 * @return Fluent
	 */
	protected function add_after($id, $after)
	{
		$items = array();
		$item  = new Fluent(array(
			'id'     => $id,
			'childs' => array()
		));

		$keys     = array_keys($this->items);
		$position = array_search($after, $keys);

		if (is_null($position)) return $this->add_parent($id);

		$position++;

		foreach ($keys as $key => $fluent)
		{
			if ($key === $position)
			{
				$items[$id] = $item;
			}

			$items[$fluent] = $this->items[$fluent];
		}

		$this->items = $items;

		return $item;
	}

	/**
	 * Add item as child of $parent
	 *
	 * @access protected
	 * @param  string   $id
	 * @param  string   $parent
	 * @return Fluent
	 */
	protected function add_child($id, $parent)
	{
		// it might be possible parent is not defined due to ACL, 
		// in this case we should simply ignore this request as child 
		// should inherit parent ACL access
		if ( ! isset($this->items[$parent])) return null;

		$item = $this->items[$parent]->childs;

		$item[$id] = new Fluent(array(
			'id' => $id,
		));

		$this->items[$parent]->childs($item);

		return $item[$id];
	}

	/**
	 * Add item as parent
	 *
	 * @access protected
	 * @param  string   $id
	 * @return Fluent
	 */
	protected function add_parent($id)
	{
		return $this->items[$id] = new Fluent(array(
			'id'     => $id,
			'childs' => array(),
		));
	}

	/**
	 * Add a new item, prepending or appending
	 *
	 * @access  public
	 * @param   string  $id
	 * @param   string  $prepend
	 * @return  self
	 */
	public function add($id, $location = 'parent')
	{
		preg_match('/^(before|after|child_?of):(.+)$/', $location, $matches);

		switch (true)
		{
			case count($matches) >= 3 and $matches[1] === 'before' :
				return $this->add_before($id, $matches[2]);
				break;
			
			case count($matches) >= 3 and $matches[1] === 'after' :
				return $this->add_after($id, $matches[2]);
				break;

			case count($matches) >= 3 and in_array($matches[1], array('childof', 'child_of')) :
				return $this->add_child($id, $matches[2]);
				break;
			
			default :
				return $this->add_parent($id);
				break;
		}
	}

	/**
	 * Render widget as a view
	 *
	 * @access  public
	 * @return  string
	 */
	public abstract function render();

	public function __get($key)
	{
		if ($key === 'items') return $this->items;
	}
}