<?php 

class Orchestra_Extensions_Controller extends Orchestra\Controller 
{
	/**
	 * Construct Extensions Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		$this->filter('before', 'orchestra::manage');
		
		Event::fire('orchestra.started: backend');
	}

	/**
	 * List all available extensions
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$data = array(
			'extensions' => Orchestra\Extension::detect(),
		);

		return View::make('orchestra::extensions.index', $data);
	}

	/**
	 * Activate an extension
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_activate($name = null)
	{
		if (is_null($name)) return Event::first('404');

		Orchestra\Extension::activate($name);

		$m = new Orchestra\Messages;
		$m->add('success', __('orchestra::response.extensions.activate', array('name' => $name)));

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}

	/**
	 * Deactivate an extension
	 *
	 * @access public
	 * @param  string   $name name of the extension
	 * @return Response
	 */
	public function get_deactivate($name = null)
	{
		if (is_null($name)) return Event::first('404');

		Orchestra\Extension::deactivate($name);

		$m = new Orchestra\Messages;
		$m->add('success', __('orchestra::response.extensions.deactivate', array('name' => $name)));

		return Redirect::to(handles('orchestra::extensions'))
				->with('message', $m->serialize());
	}
}