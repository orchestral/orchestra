<?php namespace Orchestra\Support;

use \Closure,
	\InvalidArgumentException,
	\Config,
	\Input,
	\Lang,
	Orchestra\View;

class Table {

	/**
	 * All of the registered table names.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Create a new Table instance
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = Table::make(function ($table) {
	 *			$table->with(User::all());
	 *
	 *			$table->column('username');
	 *			$table->column('password');
	 * 		});
	 * </code>
	 *
	 * @static
	 * @access  public
	 * @param   Closure     $callback
	 * @return  Table
	 */
	public static function make(Closure $callback)
	{
		return new static($callback);
	}

	/**
	 * Create a new table instance of a named table.
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = Table::of('user-table', function ($table) {
	 *			$table->with(User::all());
	 *
	 *			$table->column('username');
	 *			$table->column('password');
	 * 		});
	 * </code>
	 *
	 * @static
	 * @access   public
	 * @param    string	    $name
	 * @param    Closure	$callback
	 * @return   Table
	 */
	public static function of($name, Closure $callback = null)
	{
		if ( ! isset(static::$names[$name]))
		{
			static::$names[$name] = new static($callback);

			static::$names[$name]->name = $name;
		}

		return static::$names[$name];
	}
	
	/**
	 * Name of table.
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Table\Grid instance.
	 *
	 * @var Orchestra\Support\Table\Grid
	 */
	protected $grid = null;

	/**
	 * Create a new Table instance.
	 * 			
	 * @access public
	 * @param  Closure      $callback
	 * @return void	 
	 */
	public function __construct(Closure $callback)
	{
		// Initiate Table\Grid, this wrapper emulate table designer
		// script to create the table.
		$this->grid = new Table\Grid(Config::get('orchestra::support.table', array()));
		
		$this->extend($callback);	
	}

	/**
	 * Extend Table designer 
	 *
	 * @access public
	 * @param  Closure $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// Run the table designer.
		call_user_func($callback, $this->grid);
	}

	/**
	 * Magic method to get Table\Grid instance.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('grid', 'name'))) 
		{
			throw new InvalidArgumentException(
				"Unable to get property [{$key}]."
			);
		}
		
		return $this->{$key};
	}

	/**
	 * An alias to render()
	 *
	 * @access  public
	 * @see     render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Render the table
	 *
	 * @access  public
	 * @return  string
	 */
	public function render()
	{
		// localize Table\Grid object
		$grid  = $this->grid;
		
		// Add paginate value for current listing while appending query string
		$input = Input::query();

		// we shouldn't append ?page
		if (isset($input['page'])) unset($input['page']);

		$paginate = (true === $grid->paginate ? $grid->model->appends($input)->links() : '');

		$empty_message = $grid->empty_message;

		if ( ! ($empty_message instanceof Lang))
		{
			$empty_message = Lang::line($empty_message);
		}

		$data = array(
			'table_markup'  => $grid->markup,
			'row_markup'    => $grid->rows->markup,
			'empty_message' => $empty_message,
			'columns'       => $grid->columns(),
			'rows'          => $grid->rows(),
			'pagination'    => $paginate,
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
}
