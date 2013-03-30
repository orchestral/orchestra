<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	Response,
	View;

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
	 * @param  array    $data
	 * @return string
	 */
	public function compose_html($data)
	{
		if ( ! isset($data['view']))
		{
			throw new InvalidArgumentException("Missing [\$view].");
		}

		if ( ! ($data['view'] instanceof View))
		{
			$data['view'] = View::make($data['view']);
		}

		$view = $data['view'];
		unset($data['view']);

		return $view->with($data);
	}

	/**
	 * Compose json
	 *
	 * @access public
	 * @param  array    $data
	 * @return string
	 */
	public function compose_json($data)
	{
		unset($data['view']);

		return Response::json($data);
	}
}