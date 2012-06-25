<?php

class Orchestra_Admin_Home_Controller extends Controller
{
	public $restful = true;

	public function get_index()
	{
		return Response::make('ok', 200);
	}
}