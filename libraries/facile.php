<?php namespace Orchestra;

use InvalidArgumentException,
	RuntimeException,
	IoC,
	Response;

class Facile {

	/**
	 * Lists of templates
	 */
	public static $templates = array();

	/**
	 * Create a new Facile instance.
	 *
	 * <code>
	 * 		$users  = User::paginate(30);
	 * 		$facile = Orchestra\Facile::make('default', array(
	 * 			'view'   => 'home.index',
	 * 			'data'   => array(
	 * 				'eloquent' => $users,
	 * 				'table'    => Orchestra\Presenter::user($users),
	 * 			),
	 * 			'status' => 200,
	 * 		));
	 *
	 * 		// Alternatively
	 * 		$facile = Orchestra\Facile::make('default')
	 * 			->view('home.index')
	 * 			->with(array(
	 * 				'eloquent' => $users,
	 * 				'table'    => Orchestra\Presenter::user($users),
	 * 			))->status(200)
	 * 			->format('html');
	 * 	</code>
	 *
	 * @static
	 * @access public			
	 * @param  string   $name   Name of template
	 * @param  array    $data
	 * @param  string   $format
	 * @return self
	 */
	public static function make($name, $data = array(), $format = null) 
	{
		return new static($name, $data, $format);
	}

	/**
	 * Register a template.
	 *
	 * @static
	 * @access public
	 * @param  string           $name
	 * @param  Facile\Template  $template
	 * @return void
	 */
	public static function template($name, $template) 
	{
		$resolve = value($template);

		if ( ! ($resolve instanceof Facile\Driver))
		{
			throw new RuntimeException(
				"Expected \$template to be instanceof Orchestra\Facile\Driver."
			);
		}

		static::$templates[$name] = $resolve;
	}

	/**
	 * Create a new Facile instance.
	 *
	 * @access protected	
	 * @param  string   $name   Name of template
	 * @param  array    $data
	 * @param  string   $format
	 * @return self
	 */
	protected function __construct($name, $data = array(), $format = null) 
	{
		$this->name = $name;
		$this->data = array_merge($this->data, $data);

		if ( ! isset(static::$templates[$name]))
		{
			throw new InvalidArgumentException(
				"Template [{$name}] is not available."
			);
		}

		$this->template = static::$templates[$name];
		$this->format($format);
	}

	/**
	 * Name of template.
	 *
	 * @var string
	 */
	protected $name = null;

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
		'view' => null, 
		'data' => array(), 
		'status' => 200,
	);

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
	public function render()
	{
		if (is_null($this->format)) $this->format();

		return $this->template->compose($this->format, $this->data);
	}
}