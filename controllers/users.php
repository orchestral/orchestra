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
		// Get Users (with roles) and limit it to only 30 results for 
		// pagination. Don't you just love it when pagination simply works.
		$users = Orchestra\Model\User::with('roles')->paginate(30);

		// Build users table HTML using a schema liked code structure.
		$table = Hybrid\Table::of('orchestra.user', function ($table) use ($users)
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
			'eloquent'      => $users,
			'table'         => $table,
			'resource_name' => 'Users',
		);

		return View::make('orchestra::resources.index', $data);
	}

	/**
	 * GET A User (either create or update)
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function get_view($id = null) 
	{
		$user = Orchestra\Model\User::find($id);

		if (is_null($user))
		{
			$user = new Orchestra\Model\User;
		}

		$form = Hybrid\Form::of('orchestra.user', function ($form) use ($user)
		{
			$form->row($user);
			$form->attr(array(
				'action' => URL::to('orchestra/users/view/'.$user->id),
				'method' => 'POST',
			));

			$form->fieldset('Information', function ($fieldset)
			{
				$fieldset->control('input:text', 'E-mail Address', 'email');
			});
		});

		$data = array(
			'eloquent'      => $user,
			'form'          => $form,
			'resource_name' => 'User',
		);

		return View::make('orchestra::resources.edit', $data);
	}

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