<?php 

use Orchestra\Form, 
	Orchestra\Messages, 
	Orchestra\Table,
	Orchestra\Model\Role, 
	Orchestra\Model\User;

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

		Event::fire('orchestra.started: backend');
	}

	/**
	 * List All Users Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$keyword = Input::get('q', '');
		$roles   = Input::get('roles', array());

		// Get Users (with roles) and limit it to only 30 results for 
		// pagination. Don't you just love it when pagination simply works.
		$users = User::with('roles')->where_not_null('users.id');

		if ( ! empty($roles))
		{
			$users->join('user_roles', function ($join) use ($roles)
			{
				$join->on('users.id', '=', 'user_roles.user_id');

			})->where_in('user_roles.role_id', $roles);
		}

		if ( ! empty($keyword))
		{
			$users->where(function ($query) use ($keyword)
			{
				$query->where('email', 'LIKE', $keyword)
					->or_where('fullname', 'LIKE', $keyword);
			});
		}

		$users = $users->paginate(30);

		// Build users table HTML using a schema liked code structure.
		$table = Table::of('orchestra.users', function ($table) use ($users) 
		{
			$table->empty_message = __('orchestra::label.no-data')->get();
			// Add HTML attributes option for the table.
			$table->attr('class', 'table table-bordered table-striped');

			// attach Model and set pagination option to true
			$table->with($users, true);

			// Add columns
			$table->column('id');
			
			$table->column('fullname', function ($column)
			{
				$column->label = __('orchestra::label.users.fullname')->get();
				$column->value = function ($row)
				{
					$roles = $row->roles;
					$value = array();
					
					foreach ($roles as $role)
					{
						$value[] = '<span class="label label-info">'.$role->name.'</span>';
					}
					
					return '<strong>'.$row->fullname.'</strong><br><span class="meta">'.implode(' ', $value).'</span>';
				};

			});

			$table->column('email', function ($column) 
			{
				$column->label = __('orchestra::label.users.email')->get();
				$column->value = function ($row) 
				{
					return $row->email;
				};
			});
		});

		Event::fire('orchestra.list: users', array($users, $table));

		// Once all event listening to `orchestra.list: users` is executed, we can add
		// we can now add the final column, edit and delete action for users
		$table->extend(function ($table) 
		{
			$table->column('action', function ($column) 
			{
				$column->label = '';
				$column->value = function ($row) 
				{
					$btn = array(
						'<div class="btn-group">',
						'<a class="btn btn-mini" href="'.handles('orchestra::users/view/'.$row->id).'">Edit</a>',
						Auth::user()->id !== $row->id ? '<a class="btn btn-mini btn-danger" href="'.handles('orchestra::users/delete/'.$row->id).'">Delete</a>' : '',
						'</div>',
					);

					return implode('', $btn);
				};
			});
		});

		$data = array(
			'eloquent'      => $users,
			'table'         => $table,
			'roles'         => Role::pair(),
			'resource_name' => 'Users',
		);

		return View::make('orchestra::resources.users.index', $data);
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
		$type = 'update';
		$user = User::find($id);

		if (is_null($user)) 
		{
			$type = 'create';
			$user = new User;
		}

		$form = Form::of('orchestra.users', function ($form) use ($user)
		{
			$form->row($user);
			$form->attr(array(
				'action' => handles('orchestra::users/view/'.$user->id),
				'method' => 'POST',
			));

			$form->fieldset(function ($fieldset) 
			{
				$fieldset->control('input:text', __('orchestra::label.users.email')->get(), 'email');
				$fieldset->control('input:text', __('orchestra::label.users.fullname')->get(), 'fullname');

				$fieldset->control('input:password', __('orchestra::label.users.password')->get(), 'password');

				$fieldset->control('select', __('orchestra::label.users.roles')->get(), function ($control) 
				{
					$options = Role::pair();
					
					$control->name    = 'roles[]';
					$control->options = $options;
					$control->attr    = array('multiple' => true);
					$control->value   = function ($row, $self) use ($options) 
					{
						// get all the user roles from objects
						$roles = array();

						foreach ($row->roles as $row) 
						{
							$roles[] = $row->id;
						}

						return $roles;
					};
				});
			});
		});

		Event::fire('orchestra.form: users', array($user, $form));

		$data = array(
			'eloquent'      => $user,
			'form'          => $form,
			'resource_name' => __("orchestra::title.users.{$type}")->get(),
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

		Event::fire('orchestra.validate: users', array(& $rules));

		$v = Validator::make($input, $rules);

		if ($v->fails())
		{
			return Redirect::to(handles('orchestra::users/view/'.$id))
					->with_input()
					->with_errors($v);
		}

		$type  = 'update';
		$user  = User::find($id);

		if (is_null($user)) 
		{
			$type = 'create';
			$user = new User(array(
				'password' => Hash::make($input['password'] ?: ''),
			));
		}

		$user->fullname = $input['fullname'];
		$user->email    = $input['email'];
		
		if ( ! empty($input['password'])) 
		{
			$user->password = Hash::make($input['password']);
		}

		Event::fire("orchestra.{$type}: users", array($user));
		Event::fire("orchestra.save: users", array($user));

		$m = new Messages;

		try
		{
			DB::transaction(function () use ($user, $input)
			{
				$user->save();
				$user->roles()->sync($input['roles']);
			});

			$m->add('success', __("orchestra::response.users.{$type}"));
		}
		catch (Exception $e)
		{
			$m->add('error', __('orchestra::response.db-failed', array('error' => $e->getMessage())));
		}

		return Redirect::to(handles('orchestra::users'))
				->with('message', $m->serialize());
	}

	/**
	 * GET Delete a User
	 *
	 * @access public
	 * @param  integer $id
	 * @return Response
	 */
	public function get_delete($id = null)
	{
		$user = User::find($id);

		if (is_null($id)) return Event::fire('404');

		if ($user->id === Auth::user()->id) return Event::fire('404');

		$user->delete();

		$m = Messages::make('success', __('orchestra::response.users.delete'));

		return Redirect::to(handles('orchestra::users'))
				->with('message', $m->serialize());
	}
}