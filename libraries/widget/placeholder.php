<?php namespace Orchestra\Widget;

use \Closure, 
	\Exception;

class Placeholder extends Driver {

	/**
	 * Type
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $type = 'placeholder';

	/**
	 * Configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array(
		'defaults' => array(
			'value' => '',
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

		if (starts_with($location, 'child')) $location = 'parent';

		$item = $this->nesty->add($id, $location ?: 'parent');

		if ($callback instanceof Closure)
		{
			$item->value = $callback;
		}

		return $item;
	}
}
