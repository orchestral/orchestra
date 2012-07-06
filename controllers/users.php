<?php

class Orchestra_Users_Controller extends Orchestra\Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->filter('before', 'orchestra::manage-users');
	}

	public function get_index()
	{
		$users = Orchestra\Model\User::with('roles')->paginate(30);

		$table = Hybrid\Table::make(function ($table) use ($users)
		{
			$table->attr('class', 'table table-bordered table-striped');

			$table->with($users, true);

			$table->column('id');
			$table->column('Fullname', 'fullname');
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
			'model'         => $users,
			'table'         => $table,
			'resource_name' => 'Users',
		);

		return View::make('orchestra::dashboard.resources', $data);
	}
}