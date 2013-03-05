<?php

use Orchestra\Presenter\Resource as ResourcePresenter,
	Orchestra\Resources,
	Orchestra\Site,
	Orchestra\View;

class Orchestra_Resources_Controller extends Orchestra\Controller {

	/**
	 * Construct Resources Controller, only authenticated user should be able
	 * to access this controller.
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
	 * Route to Resources List
	 *
	 * @access private
	 * @param  array    $resources
	 * @return Response
	 */
	private function index_page($resources)
	{
		$model = array();

		foreach ($resources as $name => $resource)
		{
			if (false === value($resource->visible)) continue;
			
			$model[$name] = $resource;
		}

		Site::set('title', __('orchestra::title.resources.list'));
		Site::set('description', __('orchestra::title.resources.list-detail'));

		return View::make('orchestra::resources.index', array(
			'table' => ResourcePresenter::table($model),
		));
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

		unset($method);

		$action    = array_shift($arguments) ?: 'index';
		$content   = null;
		$resources = Resources::all();

		switch (true)
		{
			case ($name === 'index' and $name === $action) :
				return $this->index_page($resources);
				break;
			default :
				$content = Resources::call($name, $action, $arguments);
				break;
		}

		return Resources::response($content, function ($content) 
			use ($resources, $name, $action)
		{
			( ! str_contains($name, '.')) ?
				$namespace = $name : list($namespace,) = explode('.', $name, 2);

			return View::make('orchestra::resources.resources', array(
				'content'   => $content,
				'resources' => array(
					'list'      => $resources,
					'namespace' => $namespace,
					'name'      => $name,
					'action'    => $action,
				),
			));
		});
	}
}
