<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	Response,
	Orchestra\View;

class Template extends Driver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $format = array('html', 'json');

	/**
	 * Default format
	 *
	 * @var string
	 */
	protected $default_format = 'html';

	/**
	 * Compose HTML
	 *
	 * @access public
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return string
	 */
	public function compose_html($view = null, $data = array(), $status = 200)
	{
		if ( ! isset($view))
		{
			throw new InvalidArgumentException("Missing [\$view].");
		}

		if ( ! ($view instanceof View)) $view = View::make($view);

		return Response::make($view->with($data), $status);
	}

	/**
	 * Compose json
	 *
	 * @access public
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer  $status
	 * @return string
	 */
	public function compose_json($view = null, $data = array(), $status = 200)
	{
		$data = array_map(array($this, 'transform'), $data);

		return Response::json($data, $status);
	}
}