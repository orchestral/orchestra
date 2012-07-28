<?php

class Orchestra_Dashboard_Controller extends Orchestra\Controller
{
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

		Event::fire('orchestra.started: backend');
	}

	/**
	 * Dashboard Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		return View::make('orchestra::resources.dashboard');
	}
}