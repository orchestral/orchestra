<?php

use Orchestra\Extension,
	Orchestra\Extension\Publisher,
	Orchestra\Messages;

class Orchestra_Bundles_Controller extends Orchestra\Controller {
	
	/**
	 * Construct Settings Controller, only authenticated user should be able
	 * to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
		$this->filter('before', 'orchestra::manage');
	}

	/**
	 * Update a bundle.
	 *
	 * @access public
	 * @param  string   $name   Bundle name
	 * @return Response
	 */
	public function get_update($name)
	{
		// we should only be able to upgrade extension which is already
		// started
		if ( ! Bundle::started($name))
		{
			return Response::error('404');
		}

		$msg = Messages::make();

		try
		{
			Extension::publish($name);
		}
		catch (Orchestra\Extension\FilePermissionException $e)
		{
			Publisher::queue($name);
			
			// In events where extension can't be activated due to 
			// bundle:publish we need to put this under queue.
			return Redirect::to(handles('orchestra::publisher'));
		}

		$msg->add('success', __('orchestra::response.extensions.update', compact('name')));

		return Redirect::to(handles('orchestra'));
	}
}