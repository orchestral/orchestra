<?php

class Orchestra_Dashboard_Controller extends Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::installed');
		View::share('memory', Orchestra\Core::memory());
	}
	
	public function get_index()
	{
		return View::make('orchestra::dashboard.index');
	}
}