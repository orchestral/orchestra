<?php namespace Orchestra\Widget;

use \Closure;

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
	 * Render doesn't do anything at the moment but instead just
	 * comply with the abstract class from Orchestra\Widget\Driver
	 *
	 * @access public
	 * @return void
	 */
	public function render() {}

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
		
		$item = $this->traverse->add($id, $location ?: 'parent');

		if ($callback instanceof Closure)
		{
			$item->value = $callback;
		}

		return $item;
	}
}