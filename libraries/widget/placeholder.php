<?php namespace Orchestra\Widget;

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
	 * @return mixed
	 */
	public function add($id, $location = null)
	{
		$item        = $this->traverse->add($id, 'parent');
		$item->value = $location;

		return $item;
	}
}