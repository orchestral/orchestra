<?php 

class Orchestra_Extensions_Controller extends Orchestra\Controller 
{
	public function get_index()
	{
		$data = array(
			'extensions' => Orchestra\Extension::detect(),
		);

		return View::make('orchestra::extensions.index', $data);
	}
}