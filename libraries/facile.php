<?php namespace Orchestra;

use InvalidArgumentException,
	RuntimeException,
	IoC,
	Response;

class Facile {

	/**
	 * Lists of templates
	 *
	 * @var array
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
	 * 				'users' => $users,
	 * 			),
	 * 			'status' => 200,
	 * 		));
	 *
	 * 		// Alternatively
	 * 		$facile = Orchestra\Facile::make('default')
	 * 			->view('home.index')
	 * 			->with(array(
	 * 				'users' => $users,
	 * 			))
	 * 			->status(200)
	 * 			->template(new Orchestra\Facile\Template\Driver)
	 * 			->format('html');
	 * </code>
	 *
	 * @static
	 * @access public			
	 * @param  string   $name   Name of template
	 * @param  array    $data
	 * @param  string   $format
	 * @return Orchestra\Facile\Response
	 */
	public static function make($name, $data = array(), $format = null)
	{
		return new Facile\Response(static::get($name), $data, $format);
	}

	/**
	 * Create a new Facile instance helper via view.
	 *
	 * <code>
	 * 		$users  = User::paginate(30);
	 * 		$facile = Orchestra\Facile::view('home.index', array(
	 * 				'users' => $users,
	 * 			))
	 * 			->status(200)
	 * 			->template(new Orchestra\Facile\Template\Driver)
	 * 			->format('html');
	 * </code>
	 *
	 * @static
	 * @access public
	 * @return Orchestra\Facile\Response
	 */
	public static function view($view, $data = array())
	{
		return with(new Facile\Response(static::get('default')))
			->view($view)
			->with($data);
	}

	/**
	 * Create a new Facile instance helper via with.
	 *
	 * <code>
	 * 		$users  = User::paginate(30);
	 * 		$facile = Orchestra\Facile::with(array(
	 * 				'users' => $users,
	 * 			))
	 * 			->view('home.index')
	 * 			->status(200)
	 * 			->template(new Orchestra\Facile\Template\Driver)
	 * 			->format('html');
	 * </code>
	 *
	 * @static
	 * @access public
	 * @param  mixed    $data
	 * @return Orchestra\Facile\Response
	 */
	public static function with($data)
	{
		$response = new Facile\Response(static::get('default'));

		return call_user_func_array(array($response, 'with'), func_get_args());
	}

	/**
	 * Register a template.
	 *
	 * @access public							
	 * @param  string                           $name
	 * @param  Orchestra\Facile\Template\Driver $callback
	 * @return void
	 * @throws RuntimeException     If $callback not instanceof 
	 *                              Orchestra\Facile\Template\Driver
	 */
	public static function template($name, $template) 
	{
		$resolve = value($template);

		if ( ! ($resolve instanceof Facile\Template\Driver))
		{
			throw new RuntimeException(
				"Expected \$template to be instanceof Orchestra\Facile\Template\Driver."
			);
		}

		static::$templates[$name] = $resolve;
	}

	/**
	 * Get the template.
	 *
	 * @static
	 * @access public
	 * @param  string   $name
	 * @return Orchestra\Facile\Template\Driver
	 * @throws InvalidArgumentException     If template is not defined.
	 */
	public static function get($name)
	{
		if ( ! isset(static::$templates[$name]))
		{
			throw new InvalidArgumentException(
				"Template [{$name}] is not available."
			);
		}

		return static::$templates[$name];
	}
}