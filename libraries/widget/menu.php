<?php namespace Orchestra\Widget;

class Menu extends Driver {
	
	/**
	 * Type
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $type = 'menu';

	/**
	 * Configuration
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $config = array(
		'defaults' => array(
			'title'   => '',
			'link'    => '#',
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
		return $this->traverse->add($id, $location);
	}
}