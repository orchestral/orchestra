<?php namespace Orchestra\Facile\Template;

use RuntimeException,
	Input,
	Response,
	Orchestra\View;

abstract class Driver {

	/**
	 * List of supported format.
	 * 
	 * @var array
	 */
	protected $formats = array('html');

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
		if ( ! in_array($format, $this->formats))
		{
			return call_user_func(array($this, "compose_error"), null, null, 406);
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
	 * Compose an error template.
	 *
	 * @access public 	
	 * @param  mixed    $view
	 * @param  array    $data
	 * @param  integer 	$status
	 * @return Response  
	 */
	public function compose_error($view, $data = array(), $status = 404)
	{
		$view = "{$status} Error";

		if (View::exists("error.{$status}")) $view = View::make("error.{$status}");

		return Response::make($view, $status);
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
			case ($item instanceof \Laravel\Database\Eloquent\Model) :
				// passthru
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
						
			case (is_array($item)) :
				return array_map(array($this, 'transform'), $item);

			default :
				return $item;
		}
	}
}