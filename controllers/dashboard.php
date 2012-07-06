<?php

class Orchestra_Dashboard_Controller extends Orchestra\Controller
{
	public function get_index()
	{
		return View::make('orchestra::dashboard.index');
	}
}