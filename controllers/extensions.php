<?php 

class Orchestra_Extensions_Controller extends Orchestra\Controller 
{
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

		return Redirect::to('orchestra/extensions');
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

		return Redirect::to('orchestra/extensions');
	}
}