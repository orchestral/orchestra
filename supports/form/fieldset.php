<?php namespace Orchestra\Support\Form;

use \Closure, 
	\InvalidArgumentException,
	\Config, 
	\Input, 
	Laravel\Form as F, 
	Orchestra\Support\Fluent, 
	\Lang,
	\Str, 
	Orchestra\Support\HTML;

class Fieldset {

	/**
	 * Fieldset name
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Configurations
	 *
	 * @var  array
	 */
	protected $config = array();

	/**
	 * Fieldset HTML attributes
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * All the controls
	 *
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Key map for column overwriting
	 *
	 * @var array
	 */
	protected $key_map = array();

	/**
	 * Create a new Fieldset instance
	 *
	 * @access  public
	 * @return  void
	 */
	public function __construct($name, Closure $callback = null) 
	{
		if ($name instanceof Closure)
		{
			$callback = $name;
			$name     = null;
		}
		
		if ( ! empty($name)) $this->legend($name);

		// cached configuration option
		$this->config = Config::get('orchestra::support.form.fieldset');

		call_user_func($callback, $this);
	}

	/**
	 * Add or append fieldset HTML attributes
	 *
	 * @access  public
	 * @param   mixed       $key
	 * @param   mixed       $value
	 * @return  void
	 */
	public function attributes($key = null, $value = null)
	{
		switch (true)
		{
			case is_null($key) :
				return $this->attributes;
				break;

			case is_array($key) :
				$this->attributes = array_merge($this->attributes, $key);
				break;

			default :
				$this->attributes[$key] = $value;
				break;
		}
	}

	/**
	 * Append a new control to the table.
	 *
	 * <code>
	 *		// add a new control using just field name
	 *		$fieldset->control('input:text', 'username');
	 *
	 *		// add a new control using a label (header title) and field name
	 *		$fieldset->control('input:email', 'E-mail Address', 'email');
	 *
	 *		// add a new control by using a field name and closure
	 *		$fieldset->control('input:text', 'fullname', function ($control)
	 *		{
	 *			$control->label = 'User Name';
	 *
	 * 			// this would output a read-only output instead of form.
	 *			$control->field = function ($row) { 
	 * 				return $row->first_name.' '.$row->last_name; 
	 * 			};
	 *		});
	 * </code>
	 *
	 * @access  public			
	 * @param   mixed       $name
	 * @param   mixed       $callback
	 * @return  Fluent
	 */
	public function control($type, $name, $callback = null)
	{
		if ($name instanceof Lang) $name = $name->get();
		
		$label   = $name;
		$config  = $this->config;

		switch (true)
		{
			case ! is_string($label) :
				$callback = $label;
				$label    = '';
				$name     = '';
				break;
			case is_string($callback) :
				$name     = Str::lower($callback);
				$callback = null;
				break;
			default :
				$name  = Str::lower($name);
				$label = Str::title($name);
				break;
		}

		$control = new Fluent(array(
			'id'         => $name,
			'name'       => $name,
			'value'      => null,
			'label'      => $label,
			'attributes' => array(),
			'options'    => array(),
			'checked'    => false,
			'field'      => null,
		));

		// run closure
		if (is_callable($callback)) call_user_func($callback, $control);

		$field = function ($row, $control, $templates = array()) use ($type, $config) 
		{
			// prep control type information
			$type    = ($type === 'input:password' ? 'password' : $type);
			$methods = explode(':', $type);

			$templates = array_merge(
				Config::get('orchestra::support.form.templates', array()), 
				$templates
			);
			
			// set the name of the control
			$name = $control->name;
			
			// set the value from old input, follow by row value.
			$value = Input::old($name);

			if (! is_null($row->{$name}) and is_null($value)) $value = $row->{$name};

			// if the value is set from the closure, we should use it instead of 
			// value retrieved from attached data
			if ( ! is_null($control->value)) $value = $control->value;

			// should also check if it's a closure, when this happen run it.
			if ($value instanceof Closure) $value = $value($row, $control);

			$data = new Fluent(array(
				'method'     => '',
				'type'       => '',
				'options'    => array(),
				'checked'    => false,
				'attributes' => array(),
				'name'       => $name,
				'value'      => $value,
			));

			switch (true)
			{
				case (in_array($type, array('select', 'input:select'))) :
					// set the value of options, if it's callable run it first
					$options = $control->options;
					
					if ($options instanceof Closure) $options = $options($row, $control);

					$data->method('select')
						->attributes(HTML::decorate($control->attributes, $config['select']))
						->options($options);
					break;
				
				case (in_array($type, array('checkbox', 'input:checkbox'))) :
					$data->method('checkbox')
						->checked($control->checked);
					break;
				
				case (in_array($type, array('radio', 'input:radio'))) :
					$data->method('radio')
						->checked($control->checked);
					break;
				
				case (in_array($type, array('textarea', 'input:textarea'))):
					$data->method('textarea')
						->attributes(HTML::decorate($control->attributes, $config['textarea']));
					break;
				
				case (in_array($type, array('password', 'input:password'))) :
					$data->method('password')
						->attributes(HTML::decorate($control->attributes, $config['password']));
					break;

				case (in_array($type, array('file', 'input:file'))) :
					$data->method('file')
						->attributes(HTML::decorate($control->attributes, $config['file']));
					break;
				
				case (isset($methods[0]) and $methods[0] === 'input') :
					$methods[1] = $methods[1] ?: 'text';
					$data->method('input')
						->type($methods[1])
						->attributes(HTML::decorate($control->attributes, $config['input']));
					break;
				
				default :
					$data->method('input')
						->type('text')
						->attributes(HTML::decorate($control->attributes, $config['input']));

			}

			return Fieldset::render($templates, $data);
		};

		 ! is_null($control->field) or $control->field = $field;

		$this->controls[]     = $control;
		$this->key_map[$name] = count($this->controls) - 1;

		return $control;
	}

	/**
	 * Render the field.
	 *
	 * @static
	 * @access public
	 * @param  array            $templates
	 * @param  Laravel\Fluent   $data
	 * @return string
	 */
	public static function render($templates, $data)
	{
		if ( ! isset($templates[$data->method]))
		{
			throw new InvalidArgumentException(
				"Form template for [{$data->method}] is not available."
			);
		}

		return call_user_func($templates[$data->method], $data);
	}

	/**
	 * Allow control overwriting
	 *
	 * @access public
	 * @param  string   $name
	 * @param  mixed    $callback
	 * @return Fluent
	 */
	public function of($name, $callback = null)
	{
		if ( ! isset($this->key_map[$name]))
		{
			throw new InvalidArgumentException("Control name [{$name}] is not available.");
		}

		$id = $this->key_map[$name];

		if (is_callable($callback)) call_user_func($callback, $this->controls[$id]);

		return $this->controls[$id];
	}

	/**
	 * Set Fieldset Legend name
	 *
	 * <code>
	 *     $fieldset->legend('User Information');
	 * </code>
	 * 
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function legend($name = null) 
	{
		if (is_null($name)) return $this->name;

		$this->name = $name;
	}

	/**
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		if ( ! in_array($method, array('controls', 'name')))
		{
			throw new InvalidArgumentException("Unable to use __call for [{$method}].");
		}

		return $this->$method;
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		$key = $this->key($key);

		if ( ! in_array($key, array('attributes', 'name', 'controls')))
		{
			throw new InvalidArgumentException("Unable to use __get for [{$key}].");
		}

		return $this->{$key};
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, $values)
	{
		$key = $this->key($key);

		if ( ! in_array($key, array('attributes')))
		{
			throw new InvalidArgumentException("Unable to use __set for [{$key}].");
		}
		elseif ( ! is_array($values))
		{
			throw new InvalidArgumentException("Require values to be an array.");
		}

		$this->attributes($values, null);
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		$key = $this->key($key);

		if ( ! in_array($key, array('attributes', 'name', 'controls')))
		{
			throw new InvalidArgumentException("Unable to use __isset for [{$key}].");
		}

		return isset($this->{$key});
	}

	/**
	 * Valid key for magic methods.
	 *
	 * @access private 	
	 * @param  string   $key
	 * @return string
	 */
	private function key($key)
	{
		return $key;
	}
}