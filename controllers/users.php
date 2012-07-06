<?php

class Orchestra_Users_Controller extends Orchestra\Controller
{
	/**
	 * Construct Users Controller with some pre-define configuration 
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->filter('before', 'orchestra::manage-users');
	}

	/**
	 * List All Users Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		// Get Users (with roles) and limit it to only 30 results
		// for pagination. Don't you just love it when pagination 
		// simply works.
		$users = Orchestra\Model\User::with('roles')->paginate(30);

		// Build users table HTML using a schema liked code structure.
		$table = Hybrid\Table::make(function ($table) use ($users)
		{
			// Add HTML attributes option for the table.
			$table->attr('class', 'table table-bordered table-striped');

			// attach Model and set pagination option to true
			$table->with($users, true);

			// Add columns
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
					return HTML::link('orchestra/users/view/'.$row->id, 'Edit');
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

	/**
	 * GET A User (either create or update)
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function get_view($id = null) {}

	/**
	 * POST A User (either create or update)
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function post_view($id = null) {}

	/**
	 * POST A User (either create or update)
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function delete_view() {}
}