<?php namespace Orchestra\Support;

use \Closure, 
	\Config,
	\Lang,
	Orchestra\View;

class Form extends Builder {

	/**
	 * All of the registered form names.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Create a new form instance of a named form.
	 *
	 * <code>
	 *		// Create a new table instance
	 *		$view = Form::of('user-form', function ($form) {
	 *		
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
	 * Create a new Form instance
	 *
	 * @access  public
	 * @param   Closure     $callback
	 * @return  void
	 */
	public function __construct(Closure $callback)
	{
		// Instantiate Form\Grid
		$this->grid = new Form\Grid(Config::get('orchestra::support.form', array()));

		$this->extend($callback);
	}

	/**
	 * Render the form
	 *
	 * @access  public
	 * @return  string
	 */
	public function render() 
	{
		// Localize Grid instance.
		$grid       = $this->grid;
		$attributes = $grid->attributes;

		// Build Form attribute, action and method should be unset from attr 
		// as it is build using Form::open().
		$form_method = $attributes['method'];
		$form_action = $attributes['action'];

		unset($attributes['method']);
		unset($attributes['action']);

		$submit_button = $grid->submit_button;

		if ( ! ($submit_button instanceof Lang))
		{
			$submit_button = Lang::line($submit_button);
		}

		$data = array(
			'token'         => $grid->token,
			'hiddens'       => $grid->hiddens,
			'row'           => $grid->row,
			'form_action'   => $form_action,
			'form_method'   => $form_method,
			'submit_button' => $submit_button,
			'error_message' => $grid->error_message,
			'attributes'    => $attributes,
			'fieldsets'     => $grid->fieldsets(),
		);

		// Build the view and render it.
		return View::make($grid->view)->with($data)->render();
	}
}
