<?php namespace Orchestra\Widget;

use \Closure;

class Menu extends Driver {

	/**
	 * Type of Widget.
	 *
	 * @var string
	 */
	protected $type = 'menu';

	/**
	 * Widget Configuration.
	 * 
	 * @var array
	 */
	protected $config = array(
		'defaults' => array(
			'title'      => '',
			'link'       => '#',
			'attributes' => array(),
		),
	);

	/**
	 * Add an item to current widget.
	 *
	 * @access public
	 * @param  string   $id
	 * @param  mixed    $location
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function add($id, $location = 'parent', $callback = null)
	{
		if ($location instanceof Closure)
		{
			$callback = $location;
			$location = 'parent';
		}

		$item = $this->nesty->add($id, $location ?: 'parent');

		if ($callback instanceof Closure)
		{
			call_user_func($callback, $item);
		}

		return $item;
	}
}
