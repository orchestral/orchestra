<?php 

class Orchestra_Forgot_Controller extends Orchestra\Controller 
{
	/**
	 * Construct Forgot Password Controller with some pre-define configuration 
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::not-auth');
	}

	public function get_index()
	{
		return View::make('orchestra::forgot.index');
	}
}