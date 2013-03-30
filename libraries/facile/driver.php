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
	public function compose($format, $data = array())
	{
		if ( ! in_array($format, $this->format))
		{
			throw new InvalidArgumentException("Format [{$format}] is not supported.");
		}
		elseif ( ! method_exists($this, "compose_{$format}"))
		{
			throw new RuntimeException("Call to undefine method [compose_{$format}].");
		}

		return call_user_func(array($this, "compose_{$format}"), $data);
	}
}