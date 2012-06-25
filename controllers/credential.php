<?php

class Orchestra_Credential_Controller extends Controller
{
	public $restful = true;

	public function get_login()
	{
		return Response::make('login', 200);
	}
}