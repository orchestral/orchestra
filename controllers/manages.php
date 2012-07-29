<?php

use Orchestra\Extension;

class Orchestra_Manages_Controller extends Orchestra\Controller
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
		$this->filter('before', 'orchestra::manage');
		
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

		// we first check if $name actually an extension, if not we should 
		// consider it's pointing to 'application'
		if ( ! Extension::started($name) and Extension::started(DEFAULT_BUNDLE))
		{
			$action = $name;
			$name   = DEFAULT_BUNDLE;
		}
		else
		{
			$action = array_shift($arguments);
		}

		if ( ! Extension::started($name) or is_null($action))
		{
			return Response::error('404');
		}

		$content = Event::first("orchestra.manages: {$name}.{$action}", $arguments);

		if (false === $content) return Response::error('404');

		return View::make('orchestra::resources.pages', array('content' => $content));
	}
}