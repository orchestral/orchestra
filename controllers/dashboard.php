<?php

class Orchestra_Dashboard_Controller extends Orchestra\Controller
{
	/**
	 * Dashboard Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		return View::make('orchestra::dashboard.index');
	}
}