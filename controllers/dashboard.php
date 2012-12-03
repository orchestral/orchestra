<?php

use Orchestra\View,
	Orchestra\Widget;

class Orchestra_Dashboard_Controller extends Orchestra\Controller {

	/**
	 * Construct Dashboard Controller, only authenticated user should be
	 * able to access this controller.
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
		$panes = Widget::make('pane.orchestra')->get();

		return View::make('orchestra::dashboard.index', compact('panes'));
	}
}
