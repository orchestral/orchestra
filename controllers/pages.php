<?php

use Orchestra\Extension,
	Orchestra\Resources,
	Orchestra\View;

class Orchestra_Pages_Controller extends Orchestra\Controller {

	/**
	 * Construct Pages Controller, only authenticated user should be able to
	 * access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
	}

	/**
	 * Add a drop-in page anywhere on Orchestra
	 *
	 * @access public
	 * @param  string   $request
	 * @param  array    $arguments
	 * @return Response
	 */
	public function __call($request, $arguments)
	{
		$name = $action = null;

		list($method, $fragment) = explode('_', $request, 2);

		// we first check if $name actually an extension, if not we should
		// consider it's pointing to 'application'
		if (str_contains($fragment, '.'))
		{
			list($name, $action) = explode('.', $fragment, 2);

			Extension::started($name) or $name = DEFAULT_BUNDLE;
		}
		else
		{
			$name = $fragment;
		}

		if ( ! Extension::started($name))
		{
			$action = $fragment;
			$name   = DEFAULT_BUNDLE;
		}

		if (empty($action) and count($arguments) > 0)
		{
			$action = array_shift($arguments);
		}

		// We shouldn't handle any event that is not associated with a valid
		// extension
		if ( ! Extension::started($name) or is_null($action)) return Response::error('404');

		// Let get the first event associated to the requested keyword.
		$content = Event::first("orchestra.pages: {$name}.{$action}", $arguments);

		return Resources::response($content, function ($content)
		{
			return View::make('orchestra::resources.pages', compact('content'));
		});	
	}
}
