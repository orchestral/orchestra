<?php

class Orchestra_Installer_Controller extends Controller 
{
	public $restful = true;

	public function get_index() 
	{
		return Response::make('ok', 200);
	}
}