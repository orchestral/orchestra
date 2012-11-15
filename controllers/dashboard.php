<?php

use Orchestra\View;

class Orchestra_Dashboard_Controller extends Orchestra\Controller {

	/**
	 * Construct Dashboard Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
	}

	/**
	 * Dashboard Page
	 *
	 * GET (:bundle)
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$panes = Orchestra\Widget::make('pane.orchestra')->get();

		return View::make('orchestra::resources.dashboard', compact('panes'));
	}
}