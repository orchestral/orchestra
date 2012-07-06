<?php

class Orchestra_Account_Controller extends Orchestra\Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
	}

	public function get_index()
	{
		$auth = Auth::user();

		$data = array(
			'user' => Orchestra\Model\User::find($auth->id),
		);

		return View::make('orchestra::account.profile', $data);
	}

	public function get_password()
	{
		$auth = Auth::user();

		$data = array(
			'user' => Orchestra\Model\User::find($auth->id),
		);

		return View::make('orchestra::account.password', $data);
	}
}