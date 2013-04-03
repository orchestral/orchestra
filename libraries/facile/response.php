<?php namespace Orchestra\Facile;

use InvalidArgumentException,
	Orchestra\Facile as F;

class Response {

	/**
	 * View template.
	 *
	 * @var Facile\Template
	 */
	protected $template = null;

	/**
	 * View format.
	 *
	 * @var string
	 */
	protected $format = null;

	/**
	 * View data.
	 *
	 * @var array
	 */
	protected $data = array(
		'view'   => null, 
		'data'   => array(), 
		'status' => 200,
	);

	/**
	 * Create a new Facile instance.
	 *
	 * @access public
	 * @param  Orchestra\FacileTemplate\Driver  $template
	 * @param  array                            $data
	 * @param  string                           $format
	 * @return void
	 */
	public function __construct(Template\Driver $template, $data = array(), $format = null) 
	{
		$this->template = $template;
		$this->data     = array_merge($this->data, $data);

		$this->format($format);
	}

	/**
	 * Nest a view to facile.
	 *
	 * @access public
	 * @param  string   $view
	 * @return self
	 */
	public function view($view)
	{
		$this->data['view'] = $view;

		return $this;
	}

	/**
	 * Nest a data to facile.
	 *
	 * @access public
	 * @param  mixed    $key
	 * @param  mixed    $value
	 * @return self
	 */
	public function with($key, $value = null)
	{
		$data = is_array($key) ? $key : array($key => $value);
		
		$this->data['data'] = array_merge($this->data['data'], $data);

		return $this;
	}

	/**
	 * Set http status to facile.
	 *
	 * @access public	
	 * @param  integer  $status
	 * @return self
	 */
	public function status($status = 200)
	{
		$this->data['status'] = $status;

		return $this;
	}
	
	/**
	 * Set a template for Facile.
	 *
	 * @access public
	 * @param  mixed    $name
	 * @return self
	 */
	public function template($name)
	{
		if ($name instanceof Template\Driver) 
		{
			$this->template = $name;
		}
		else
		{
			$this->template = F::get($name);
		}

		return $this;
	}

	/**
	 * Get expected facile format.
	 *
	 * @access public
	 * @param  string   $format
	 * @return string
	 */
	public function format($format = null)
	{
		! empty($format) and $this->format = $format;

		if (is_null($this->format))
		{
			$this->format = $this->template->format();
		}

		return $this;
	}

	/**
	 * Magic method to __get.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('name', 'template', 'format')))
		{
			throw new InvalidArgumentException("Invalid request to [{$key}].");
		}

		return $this->{$key};
	}

	/**
	 * Render facile by selected format.
	 *
	 * @access public
	 * @return mixed
	 */
	public function __toString()
	{
		$facile = $this->render();

		if ( ! is_string($facile) and method_exists($facile, 'render'))
		{
			return $facile->render();
		}

		return $facile;
	}

	/**
	 * Render facile by selected format.
	 *
	 * @access public
	 * @return mixed
	 */
	public function render()
	{
		if (is_null($this->format)) $this->format();

		return $this->template->compose($this->format, $this->data);
	}
}