<?php namespace Orchestra;

use \Controller as Base_Controller, \View;

class Controller extends Base_Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::installed');

		View::share('fluent_layout', true);
		View::share('memory', Core::memory());
	}
}