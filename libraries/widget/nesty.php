<?php namespace Orchestra\Widget;

use Orchestra\Support\Fluent;

class Nesty {

	/**
	 * List of items
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $items = array();

	/**
	 * Configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array();

	/**
	 * Construct a new instance
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Create a new Fluent instance while appending default config.
	 *
	 * @access protected
	 * @param  int  $id
	 * @return Fluent
	 */
	protected function to_fluent($id)
	{
		$defaults = isset($this->config['defaults']) ?
			$this->config['defaults'] : array();

		return new Fluent(array_merge($defaults, array(
			'id'     => $id,
			'childs' => array(),
		)));
	}

	/**
	 * Add item before reference $before
	 *
	 * @static
	 * @access protected
	 * @param  string   $id
	 * @param  string   $before
	 * @return Fluent
	 */
	protected function add_before($id, $before)
	{
		$items = array();
		$found = false;
		$item  = $this->to_fluent($id);

		$keys     = array_keys($this->items);
		$position = array_search($before, $keys);

		if (is_null($position)) return $this->add_parent($id);

		if ($position > 0) $position--;

		foreach ($keys as $key => $fluent)
		{
			if ($key === $position)
			{
				$found      = true;
				$items[$id] = $item;
			}

			$items[$fluent] = $this->items[$fluent];
		}

		if ( ! $found) $items[$id] = $item;

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
		$found = false;
		$items = array();
		$item  = $this->to_fluent($id);

		$keys     = array_keys($this->items);
		$position = array_search($after, $keys);

		if (is_null($position)) return $this->add_parent($id);

		$position++;

		foreach ($keys as $key => $fluent)
		{
			if ($key === $position)
			{
				$found      = true;
				$items[$id] = $item;
			}

			$items[$fluent] = $this->items[$fluent];
		}

		if ( ! $found) $items[$id] = $item;

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
		$node = $this->descendants($parent);

		// it might be possible parent is not defined due to ACL, in this
		// case we should simply ignore this request as child should
		// inherit parent ACL access
		if ( ! isset($node)) return null;

		$item = $node->childs;
		$item[$id] = $this->to_fluent($id);

		$node->childs($item);

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
		return $this->items[$id] = $this->to_fluent($id);
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
		preg_match('/^(before|after|child[\_|-]?of):(.+)$/', $location, $matches);

		switch (true)
		{
			case count($matches) >= 3 and $matches[1] === 'before' :
				return $this->add_before($id, $matches[2]);
				break;

			case count($matches) >= 3 and $matches[1] === 'after' :
				return $this->add_after($id, $matches[2]);
				break;

			case count($matches) >= 3 and preg_match('/^child[\_|-]?of$/', $matches[1]) :
				return $this->add_child($id, $matches[2]);
				break;

			default :
				return $this->add_parent($id);
				break;
		}
	}

	/**
	 * Get node from items recursively
	 *
	 * @access protected
	 * @param  string       $key
	 * @return Fluent
	 */
	protected function descendants($key)
	{
		$array = $this->items;

		if (is_null($key)) return $array;

		$keys  = explode('.', $key);
		$array = $array[array_shift($keys)];

		// To retrieve the array item using dot syntax, we'll iterate through
		// each segment in the key and look for that value. If it exists,
		// we will return it, otherwise we will set the depth of the array
		// and look for the next segment.
		foreach ($keys as $segment)
		{
			if ( ! is_array($array->childs) or ! isset($array->childs[$segment]))
			{
				return $array;
			}

			$array = $array->childs[$segment];
		}

		return $array;
	}

	/**
	 * Return all items
	 *
	 * @access public
	 * @return array
	 */
	public function get()
	{
		return $this->items;
	}
}
