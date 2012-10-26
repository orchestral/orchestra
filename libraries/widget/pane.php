<?php namespace Orchestra\Widget;

use \Closure;

class Pane extends Driver
{
	/**
	 * Type
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $type = 'pane';

	/**
	 * Configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array(
		'defaults' => array(
			'attr'    => array(),
			'title'   => '',
			'content' => '',
			'html'    => '',
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
	 * @param  string   $location
	 * @return mixed
	 */
	public function add($id, $location = 'parent')
	{
		$item = $this->traverse->add($id, 'parent');

		if ($location instanceof Closure)
		{
			call_user_func($callback, $item);
		}

		return $item;
	}
}