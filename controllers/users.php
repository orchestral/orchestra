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
		$table = Hybrid\Table::of('orchestra.user', function ($table) use ($users) {
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
					return '
					<div class="btn-group">
						<a class="btn btn-mini" href="'.URL::to('orchestra/users/view/'.$row->id).'">Edit</a>
						<a class="btn btn-mini btn-danger" href="'.URL::to('orchestra/users/delete/'.$row->id).'">Delete</a>
					</div>';
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

		if (is_null($user)) $user = new Orchestra\Model\User;

		$form = Hybrid\Form::of('orchestra.user', function ($form) use ($user) {
			$form->row($user);
			$form->attr(array(
				'action' => URL::to('orchestra/users/view/'.$user->id),
				'method' => 'POST',
			));

			$form->fieldset(function ($fieldset) {
				$fieldset->control('input:text', 'E-mail Address', 'email');
				$fieldset->control('input:text', 'fullname');

				$fieldset->control('input:password', 'password', function ($control) {
					$control->field = function ($row, $control) {
						return Form::password($control->name);
					};
				});

				$fieldset->control('select', 'roles', function ($control) {
					$options = array();

					foreach (Orchestra\Model\Role::all() as $role) {
						$options[$role->id] = $role->name;
					}

					$control->field = function ($row, $self) use ($options) {
						// get all the user roles from objects
						$roles = array();

						foreach ($row->{$self->name} as $row) {
							$roles[] = $row->id;
						}

						return Form::select('roles[]', $options, $roles, array('multiple' => true));
					};
				});
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
	public function post_view($id = null) 
	{
		$input = Input::all();
		$rules = array(
			'email'    => array('required', 'email'),
			'fullname' => array('required'),
			'roles'    => array('required'),
		);

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to('orchestra/users/view/'.$id)
					->with_input()
					->with_errors($v);
		}

		$type  = 'updated';
		$user  = Orchestra\Model\User::find($id);

		if (is_null($user)) 
		{
			$type = 'created';
			$user = new Orchestra\Model\User(array(
				'password' => Hash::make($input['password'] ?: ''),
			));
		}

		$user->fullname = $input['fullname'];
		$user->email    = $input['email'];
		
		if ( ! empty($input['password'])) 
		{
			$user->password = Hash::make($input['password']);
		}
		
		$user->save();

		$user->roles()->sync($input['roles']);

		$m = Orchestra\Messages::make('success', __("response.users.{$type}"));

		return Redirect::to('orchestra/users')->with('message', $m->serialize());

	}

	/**
	 * POST A User (either create or update)
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function delete_view() {}
}