<?php namespace Orchestra;

use \Controller as Base_Controller, \View;

class Controller extends Base_Controller
{
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

		// All controller should be accessible only after Orchestra is installed.
		$this->filter('before', 'orchestra::installed');

		View::share('fluent_layout', true);
		View::share('orchestra_memory', Core::memory());
	}
}