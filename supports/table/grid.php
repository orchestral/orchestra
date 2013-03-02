<?php namespace Orchestra\Support\Table;

use \Closure, 
	\InvalidArgumentException,
	Orchestra\Support\Fluent, 
	\Lang,
	\Str;

class Grid {

	/**
	 * List of rows in array, is used when model is null
	 *
	 * @var array
	 */
	protected $rows = null;

	/**
	 * Eloquent model used for table
	 *
	 * @var mixed
	 */
	protected $model = null;

	/**
	 * Table HTML attributes
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * All the columns
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Key map for column overwriting
	 *
	 * @var array
	 */
	protected $key_map = array();

	/**
	 * Enable to attach pagination during rendering
	 *
	 * @var bool
	 */
	protected $paginate = false;

	/**
	 * Set the no record message
	 *
	 * @var string
	 */
	public $empty_message = null;

	/**
	 * Selected view path for table layout
	 *
	 * @var array
	 */
	protected $view = null;

	/**
	 * Create a new Grid instance
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($config = array())
	{
		foreach ($config as $key => $value)
		{
			if ( ! property_exists($this, $key)) continue;

			$this->{$key} = $value;
		}
		
		$this->rows = new Fluent(array(
			'data'       => array(),
			'attributes' => function ($row) { return array(); },
		));
	}

	/**
	 * Attach Eloquent results as row and allow pagination (if required)
	 *
	 * <code>
	 *		// add model without pagination
	 *		$table->with(User::all());
	 *
	 *		// add model with pagination
	 *		$table->with(User::paginate(30), true);
	 * </code>
	 *
	 * @access public	
	 * @param  Eloquent $model
	 * @param  bool     $paginate
	 * @return void
	 */
	public function with($model, $paginate = false)
	{
		$this->paginate = $paginate;
		$this->model    = $model;

		$this->rows(true === $paginate ? $model->results : $model);
	}

	/**
	 * Set table layout (view)
	 *
	 * <code>
	 *		// use default horizontal layout
	 *		$table->layout('horizontal');
	 *
	 * 		// use default vertical layout
	 * 		$table->layout('vertical');
	 *
	 *		// define table using custom view
	 *		$table->layout('path.to.view');
	 * </code>
	 *
	 * @access public
	 * @param  string   $name
	 * @return void
	 */
	public function layout($name)
	{
		switch ($name)
		{
			case 'horizontal' :
			case 'vertical' :
				$this->view = "orchestra::support.table.{$name}";
				break;
			default :
				$this->view = $name;
				break;
		}
	}

	/**
	 * Attach rows data instead of assigning a model
	 *
	 * <code>
	 *		// assign a data
	 * 		$table->rows(DB::table('users')->get());
	 * </code>
	 *
	 * @access public		
	 * @param  array    $rows
	 * @return void
	 */
	public function rows(array $rows = null)
	{
		if (is_null($rows)) return $this->rows->data;

		$this->rows->data = $rows;
	}

	/**
	 * Append a new column to the table.
	 *
	 * <code>
	 *		// add a new column using just field name
	 *		$table->column('username');
	 *
	 *		// add a new column using a label (header title) and field name
	 *		$table->column('User Name', 'username');
	 *
	 *		// add a new column by using a field name and closure
	 *		$table->column('fullname', function ($column)
	 *		{
	 *			$column->label = 'User Name';
	 *			$column->value = function ($row) { 
	 * 				return $row->first_name.' '.$row->last_name; 
	 * 			};
	 *
	 * 			$column->label_attributes(array('class' => 'header-class'));
	 *
	 * 			$column->cell_attributes(function ($row) { 
	 *				return array('data-id' => $row->id);
	 *			});
	 *		});
	 * </code>
	 *
	 * @access public			
	 * @param  mixed    $label
	 * @param  mixed    $callback
	 * @return Fluent
	 */
	public function column($name, $callback = null)
	{
		if ($name instanceof Lang) $name = $name->get();

		$value = '';
		$label = $name;

		switch (true)
		{
			case ! is_string($label) :
				$callback = $name;
				$name     = '';	
				$label    = '';
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

		if ( ! empty($name))
		{
			$value = function ($row) use ($name) { return $row->{$name}; };
		}
		
		$column = new Fluent(array(
			'id'               => $name,
			'label'            => $label,
			'value'            => $value,
			'escape'           => true,
			'label_attributes' => array(),
			'cell_attributes'  => function ($row) { return array(); },
		));

		// run closure
		if (is_callable($callback)) call_user_func($callback, $column);

		$this->columns[]      = $column;
		$this->key_map[$name] = count($this->columns) - 1;

		return $column;
	}

	/**
	 * Allow column overwriting
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
			throw new InvalidArgumentException("Column name [{$name}] is not available.");
		}

		$id = $this->key_map[$name];

		if (is_callable($callback)) call_user_func($callback, $this->columns[$id]);

		return $this->columns[$id];
	}

	/**
	 * Add or append table HTML attributes
	 *
	 * @access public
	 * @param  mixed    $key
	 * @param  mixed    $value
	 * @return void
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
	 * Magic Method for calling the methods.
	 */
	public function __call($method, array $arguments = array())
	{
		if ( ! in_array($method, array('columns', 'view')))
		{
			throw new InvalidArgumentException("Unable to use __call for [{$method}].");
		}

		unset($arguments);

		return $this->$method;
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		$key = $this->key($key);
		
		if ( ! in_array($key, array('attributes', 'columns', 'model', 'paginate', 'view', 'rows')))
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

		if ( ! in_array($key, array('attributes', 'columns', 'model', 'paginate', 'view')))
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
