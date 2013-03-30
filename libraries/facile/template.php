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

		$view   = $compose['view'];
		$data   = (isset($compose['data']) ? $compose['data'] : null);
		$status = (isset($compose['status']) ? $compose['status'] : 200);

		return Response::make($view->with($data), $status);
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
		$data   = $this->transform(isset($compose['data']) ? $compose['data'] : array());
		$status = (isset($compose['status']) ? $compose['status'] : 200);

		return Response::json($data, $status);
	}

	/**
	 * Transform given data
	 *
	 * @access public
	 * @param  array    $data
	 * @return array
	 */
	public function transform($data)
	{
		$to_array = function($item) 
		{ 
			return (method_exists($item, 'to_array')) ? $item->to_array() : $item; 
		};

		foreach ($data as $key => $item)
		{
			// Nested data should be render.
			if (method_exists($item, 'render')) $data[$key] = e($item->render());

			if ($item instanceof \Laravel\Paginator)
			{
				$data[$key] = array(
					'results' => array_map($to_array, $item->results),
					'links'   => e($item->links()),
				);
			}

			if (is_array($item))
			{
				$data[$key] = array_map($to_array, $item);
			}
		}

		return $data;
	}
}