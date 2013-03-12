<?php

use Orchestra\Controller,
	Orchestra\Core,
	Orchestra\Extension\Publisher,
	Orchestra\Messages,
	Orchestra\Site;

class Orchestra_Publisher_Controller extends Controller {

	/**
	 * Use restful verb.
	 *
	 * @var  string
	 */
	public $restful = true;

	/**
	 * Load dependencies during __construct
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
	 * Load publisher based on service.
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$driver = Core::memory()->get('orchestra.publisher.driver', 'ftp');
		$msg    = Messages::make();

		if (Publisher::connected()) 
		{
			Publisher::execute($msg);
		}

		return Redirect::to(handles('orchestra::publisher/ftp'));
	}
	

	/**
	 * Show FTP configuration form or run the queue.
	 *
	 * @access public
	 * @return Response
	 */
	public function get_ftp()
	{
		Site::set('title', __('orchestra::title.publisher.ftp'));
		Site::set('description', __('orchestra::title.publisher.description'));

		return View::make('orchestra::publisher.ftp');
	}

	/**
	 * POST FTP configuration and run the queue.
	 *
	 * POST (orchestra)/publisher/ftp
	 *
	 * @access public
	 * @return Response
	 */
	public function post_ftp()
	{
		$input  = Input::only(array('host', 'user', 'password'));
		$msg    = Messages::make();
		$queues = Publisher::queued();

		$input['ssl'] = (Input::get('connection-type', 'sftp') === 'sftp');

		// Make an attempt to connect to service first before
		try
		{
			Publisher::connect($input);
		}
		catch(Orchestra\Support\FTP\ServerException $e)
		{
			Session::forget('orchestra.ftp');

			$msg->add('error', $e->getMessage());

			return Redirect::to(handles('orchestra::publisher/ftp'))
				->with_input();
		}

		Session::put('orchestra.ftp', $input);

		if (Publisher::connected() and ! empty($queues))
		{
			Publisher::execute($msg);
		}

		return Redirect::to(handles('orchestra::publisher/ftp'));
	}
}