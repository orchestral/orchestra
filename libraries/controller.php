<?php namespace Orchestra;

use \Controller as Base_Controller;

class Controller extends Base_Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::installed');
		$this->filter('before', 'orchestra::manage-users');

		View::share('memory', Orchestra\Core::memory());
	}
}