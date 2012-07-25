<?php

class Orchestra_Pages_Controller extends Orchestra\Controller
{
	public $restful = true;

	/**
	 * Construct Pages Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		
		Event::fire('orchestra.started: backend');
	}

	/**
	 * Add a drop-in page anywhere on Orchestra
	 *
	 * @access public
	 * @param  string $request
	 * @param  array $arguments
	 * @return Response
	 */
	public function __call($request, $arguments)
	{
		list($method, $name) = explode('_', $request, 2);
		$action              = array_shift($arguments);

		if ( ! isset($name) or Extension::started($name) or is_null($action))
		{
			return Response::error('404');
		}

		$response = Event::first("orchestra.pages: {$name}.{$action}", $arguments);

		if (false === $response) return Response::error('404');

		return $response;
	}
}