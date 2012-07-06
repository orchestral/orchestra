<?php

class Orchestra_Users_Controller extends Controller
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
		$users = Orchestra\Model\User::with('roles')->paginate(30);

		$table = Hybrid\Table::make(function ($table) use ($users)
		{
			$table->attr('class', 'table table-bordered table-striped');

			$table->with($users, true);

			$table->column('id');
			$table->column('email', function ($column) {
				$column->heading = 'E-mail Address';
				$column->value   = function ($row) {
					return $row->email;
				};
			});

			$table->column('action', function ($column) {
				$column->heading = '';
				$column->value   = function ($row) {
					return 'Edit';
				};
			});
		});

		$data = array(
			'model' => $users,
			'table' => $table,
		);

		return View::make('orchestra::dashboard.resources', $data);
	}
}