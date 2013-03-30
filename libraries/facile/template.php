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
	public function compose_html($compose)
	{
		if ( ! isset($compose['view']))
		{
			throw new InvalidArgumentException("Missing [\$view].");
		}

		if ( ! ($compose['view'] instanceof View))
		{
			$compose['view'] = View::make($compose['view']);
		}

		$view = $compose['view'];
		$data = (isset($compose['data']) ? $compose['data'] : null);

		return $view->with($data);
	}

	/**
	 * Compose json
	 *
	 * @access public
	 * @param  array    $data
	 * @return string
	 */
	public function compose_json($compose)
	{
		$json = isset($compose['data']) ? $compose['data'] : null;

		return Response::json($json);
	}
}