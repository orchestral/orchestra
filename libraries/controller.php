<?php namespace Orchestra;

use \Controller as Base_Controller,
	\Event,
	\View as V;

class Controller extends Base_Controller {

	/**
	 * Set Orchestra\Controller to default use Restful Controller
	 *
	 * @access public
	 * @var    boolean
	 */
	public $restful = true;

	/**
	 * Construct with filter and global nested data for View
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		// All controller should be accessible only after Orchestra is
		// installed.
		$this->filter('before', 'orchestra::installed');
		
		Event::fire('orchestra.started: backend');
	}

	/**
	 * After filter for Orchestra\Controller, we primarily use this to fire
	 * `orchestra.done: backend` event.
	 *
	 * @access public
	 * @param  mixed    $response
	 * @return mixed
	 */
	public function after($response)
	{
		Event::fire('orchestra.done: backend');

		return $response;
	}
}
