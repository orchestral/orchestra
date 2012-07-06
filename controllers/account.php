<?php

class Orchestra_Account_Controller extends Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::installed');

		View::share('memory', Orchestra\Core::memory());
	}

	public function get_index()
	{
		$auth = Auth::user();

		$data = array(
			'user' => Orchestra\Model\User::find($auth->id),
		);

		return View::make('orchestra::account.profile', $data);
	}
}