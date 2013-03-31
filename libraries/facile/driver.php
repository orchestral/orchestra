<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	RuntimeException,
	Input;

abstract class Driver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $format = array('html');

	/**
	 * Default format
	 *
	 * @var string
	 */
	protected $default_format = 'html';

	/**
	 * Detect current format.
	 *
	 * @access public
	 * @return string
	 */
	public function format()
	{
		return Input::get('format', $this->default_format);
	}

	/**
	 * Compose requested format.
	 *
	 * @access public
	 * @return mixedd
	 */
	public function compose($format, $compose = array())
	{
		if ( ! in_array($format, $this->format))
		{
			throw new InvalidArgumentException("Format [{$format}] is not supported.");
		}
		elseif ( ! method_exists($this, "compose_{$format}"))
		{
			throw new RuntimeException("Call to undefine method [compose_{$format}].");
		}

		return call_user_func(
			array($this, "compose_{$format}"), 
			$compose['view'], 
			$compose['data'], 
			$compose['status']
		);
	}

	/**
	 * Transform given data
	 *
	 * @access public
	 * @param  array    $data
	 * @return array
	 */
	public function transform($item)
	{
		switch (true)
		{
			case (method_exists($item, 'to_array')) :
				return $item->to_array();

			case (method_exists($item, 'render')) :
				return e($item->render());

			case ($item instanceof \Laravel\Paginator) :
				$results = $item->results;

				is_array($results) and $results = array_map(array($this, 'transform'), $results);

				return array(
					'results' => $results,
					'links'   => e($item->links()),
				);

			default :
				return $item;
		}
	}
}