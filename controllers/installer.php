<?php namespace Orchestra;

use \Response;

class Installer_Controller extends Controller 
{
	public $restful = true;

	public function get_index() 
	{
		return Response::make('', 200);
	}
}