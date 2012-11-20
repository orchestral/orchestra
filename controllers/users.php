<?php

use Orchestra\Form,
	Orchestra\HTML,
	Orchestra\Messages,
	Orchestra\Table,
	Orchestra\View,
	Orchestra\Model\Role,
	Orchestra\Model\User;

class Orchestra_Users_Controller extends Orchestra\Controller {

	/**
	 * Construct Users Controller with some pre-define
	 * configuration
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
		$keyword = Input::get('q', '');
		$roles   = Input::get('roles', array());

		// Get Users (with roles) and limit it to only 30 results
		// for pagination. Don't you just love it when pagination
		// simply works.
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

		// Build users table HTML using a schema liked code
		// structure.
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
						$value[] = HTML::create('span', $role->name, array(
							'class' => 'label label-info',
						));
					}

					return implode('', array(
						HTML::create('strong', $row->fullname),
						HTML::create('br'),
						HTML::create('span', HTML::raw(implode(' ', $value)), array(
							'class' => 'meta',
						)),
					));
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

		// Once all event listening to `orchestra.list: users` is
		// executed, we can add we can now add the final column,
		// edit and delete action for users
		$table->extend(function ($table)
		{
			$table->column('action', function ($column)
			{
				$column->label = '';
				$column->value = function ($row)
				{
					$btn = array();
					$btn[] = HTML::link(
						handles('orchestra::users/view/'.$row->id),
						__('orchestra::label.edit')->get(),
						array('class' => 'btn btn-mini')
					);

					if (Auth::user()->id !== $row->id)
					{
						$btn[] = HTML::link(
							handles('orchestra::users/delete/'.$row->id),
							__('orchestra::label.delete')->get(),
							array('class' => 'btn btn-mini btn-danger')
						);
					}

					return HTML::create(
						'div',
						HTML::raw(implode('', $btn)),
						array('class' => 'btn-group')
					);
				};
			});
		});

		$data = array(
			'eloquent' => $users,
			'table'    => $table,
			'roles'    => Role::pair(),
			'_title_'  => 'Users',
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
				$fieldset->control('input:text', 'email', function ($control)
				{
					$control->label =  __('orchestra::label.users.email')->get();
				});

				$fieldset->control('input:text', 'fullname', function($control)
				{
					$control->label = __('orchestra::label.users.fullname')->get();
				});

				$fieldset->control('input:password', 'password', function($control)
				{
					$control->label = __('orchestra::label.users.password')->get();
				});

				$fieldset->control('select', 'roles[]', function ($control)
				{
					$options = Role::pair();

					$control->label   = __('orchestra::label.users.roles')->get();
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
		Event::fire('orchestra.form: user.account', array($user, $form));

		$data = array(
			'eloquent' => $user,
			'form'     => $form,
			'_title_'  => __("orchestra::title.users.{$type}")->get(),
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
		Event::fire('orchestra.validate: user.account', array(& $rules));

		$v = Validator::make($input, $rules);
		$m = new Messages;

		if ($v->fails())
		{
			return Redirect::to(handles('orchestra::users/view/'.$id))
					->with_input()
					->with_errors($v);
		}

		$type = 'update';
		$user = User::find($id);

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

		$this->fire_event(($type === 'create' ? 'creating' : 'updating'), $user);
		$this->fire_event('saving', $user);

		try
		{
			DB::transaction(function () use ($user, $input)
			{
				$user->save();
				$user->roles()->sync($input['roles']);
			});

			$this->fire_event(($type === 'create' ? 'created' : 'updated'), $user);
			$this->fire_event('saved', $user);

			$m->add('success', __("orchestra::response.users.{$type}"));
		}
		catch (Exception $e)
		{
			$m->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
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
		if (is_null($id)) return Event::fire('404');

		$user = User::find($id);
		$m    = new Messages;

		if ($user->id === Auth::user()->id) return Event::fire('404');

		$this->fire_event('deleting', $user);

		try
		{
			DB::transaction(function () use ($user)
			{
				$user->delete();
			});

			$this->fire_event('deleted', $user);

			$m->add('success', __('orchestra::response.users.delete'));
		}
		catch (Exception $e)
		{
			$m->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
		}

		return Redirect::to(handles('orchestra::users'))
				->with('message', $m->serialize());
	}

	/**
	 * Fire Event related to eloquent process
	 *
	 * @access private
	 * @param  string   $type
	 * @param  Eloquent $user
	 * @return void
	 */
	private function fire_event($type, $user)
	{
		Event::fire("orchestra.{$type}: users", array($user));
		Event::fire("orchestra.{$type}: user.account", array($user));
	}

}
