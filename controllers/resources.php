<?php

use Orchestra\Resources,
	Orchestra\Table;

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
	 * Route to Resources List
	 *
	 * @access private
	 * @param  array    $resources
	 * @return Response
	 */
	private function _index($resources)
	{
		$table = Table::of('orchestra.resources: list', function ($table) use ($resources)
		{
			$table->empty_message = __('orchestra::label.no-data')->get();

			// Add HTML attributes option for the table.
			$table->attr('class', 'table table-bordered table-striped');

			// attach the list
			$table->rows($resources);

			$table->column('name', function ($column)
			{
				$column->value = function ($row)
				{
					return '<strong>'.HTML::link(handles("orchestra::resources/{$row->id}"), $row->name).'</strong>';
				};
			});
		});

		return View::make('orchestra::resources.index', array(
			'table'     => $table,
			'page_name' => __('orchestra::title.resources.list')->get(),
			'page_desc' => __('orchestra::title.resources.list-detail')->get(),
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

		$action    = array_shift($arguments) ?: 'index';
		$page_name = '';
		$page_desc = '';
		$content   = "";

		$resources = Resources::all();

		switch (true) 
		{
			case ($name === 'index' and $name === $action) :
				return $this->_index($resources);
				break;
			default :
				$content = Resources::call($name, $action, $arguments);
				break;
		}

		if ( ! $content) return Response::error('404');
		elseif ($content instanceof Redirect) return $content;
		elseif ($content instanceof Response)
		{
			$status_code = $content->foundation->getStatusCode();

			if ( ! $content->foundation->isSuccessful())
			{
				return Response::error($status_code);
			}
		}

		return View::make('orchestra::resources.resources', array(
			'content'        => $content,
			'resources_list' => $resources,
			'page_name'      => $page_name,
			'page_desc'      => $page_desc,
		));
	}	
}