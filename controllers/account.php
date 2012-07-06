<?php

class Orchestra_Account_Controller extends Orchestra\Controller
{
	/**
	 * Construct Account Controller to allow user 
	 * to update own profile. Only authenticated user 
	 * should be able to access this controller.
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
	 * Edit User Profile Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$auth = Auth::user();

		$data = array(
			'user' => Orchestra\Model\User::find($auth->id),
		);

		return View::make('orchestra::account.profile', $data);
	}

	/**
	 * Edit User Profile
	 *
	 * @access public
	 * @return Response
	 */
	public function post_index()
	{

	}

	/**
	 * Edit Password Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_password()
	{
		$auth = Auth::user();

		$data = array(
			'user' => Orchestra\Model\User::find($auth->id),
		);

		return View::make('orchestra::account.password', $data);
	}

	/**
	 * Edit User Password
	 *
	 * @access public
	 * @return Response
	 */
	public function post_password()
	{
		
	}
}