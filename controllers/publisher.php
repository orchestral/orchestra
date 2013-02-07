<?php

use Orchestra\Controller,
	Orchestra\Core,
	Orchestra\Extension\Publisher,
	Orchestra\Messages;

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
		$msg    = new Messages;

		if (Publisher::connected()) 
		{
			Publisher::execute($msg);
		}

		return Redirect::to(handles('orchestra::publisher/ftp'))
				->with('message', $msg->serialize());
	}
	

	/**
	 * Show FTP configuration form or run the queue.
	 *
	 * @access public
	 * @return Response
	 */
	public function get_ftp()
	{
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
		$msg    = new Messages;
		$queues = Publisher::queued();

		$input['ssl'] = (Input::get('connection-type', 'sftp') === 'sftp');

		// Make an attempt to connect to service first before
		try
		{
			Publisher::connect($input);
			Session::put('orchestra.ftp', $input);
		}
		catch(Hybrid\FTP\ServerException $e)
		{
			Session::forget('orchestra.ftp');

			$msg->add('error', $e->getMessage());

			return Redirect::to(handles('orchestra::publisher/ftp'))
				->with('message', $msg->serialize())
				->with_input();
		}

		if (Publisher::connected() and ! empty($queues))
		{
			Publisher::execute($msg);
		}

		return Redirect::to(handles('orchestra::publisher/ftp'))
				->with('message', $msg->serialize());
	}
}