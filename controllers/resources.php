<?php

use Orchestra\Resources;

class Orchestra_Resources_Controller extends Orchestra\Controller
{
	public $restful = true;

	/**
	 * Construct Resources Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		$this->filter('before', 'orchestra::manage');
		
		Event::fire('orchestra.started: backend');
	}

	/**
	 * Add a drop-in resource anywhere on Orchestra
	 *
	 * @access public
	 * @param  string $request
	 * @param  array  $arguments
	 * @return Response
	 */
	public function __call($request, $arguments = array())
	{
		list($method, $name) = explode('_', $request, 2);

		$action  = array_shift($arguments) ?: 'index';

		switch (true) 
		{
			case ($name === 'index' and $name === $action) :	
				$content = "";
				break;
			default :
				$content = Resources::call($name, $action, $arguments);
				break;
		}

		$resources = Resources::all();

		if (false === $content) return Response::error('404');

		return View::make('orchestra::resources.resources', array(
			'content'   => $content,
			'resources' => $resources,
		));
	}	
}