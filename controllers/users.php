<?php

use Orchestra\Form,
	Orchestra\HTML,
	Orchestra\Messages,
	Orchestra\Model\Role,
	Orchestra\Model\User,
	Orchestra\Presenter\User as UserPresenter,
	Orchestra\Site,
	Orchestra\View;

class Orchestra_Users_Controller extends Orchestra\Controller {

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
		$keyword = Input::get('q', '');
		$roles   = Input::get('roles', array());

		// Get Users (with roles) and limit it to only 30 results for
		// pagination. Don't you just love it when pagination simply works.
		$users = User::search($keyword, $roles)->paginate(30);

		// Build users table HTML using a schema liked code structure.
		$table = UserPresenter::table($users);

		Event::fire('orchestra.list: users', array($users, $table));

		// Once all event listening to `orchestra.list: users` is executed,
		// we can add we can now add the final column, edit and delete action
		// for users
		UserPresenter::table_actions($table);

		$data = array(
			'eloquent' => $users,
			'table'    => $table,
			'roles'    => Role::lists('name', 'id'),
		);

		Site::set('title', __('orchestra::title.users.list'));

		return View::make('orchestra::users.index', $data);
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

		$form = UserPresenter::form($user);

		$this->fire_event('form', array($user, $form));

		$data = array(
			'eloquent' => $user,
			'form'     => $form,
		);

		Site::set('title', __("orchestra::title.users.{$type}"));

		return View::make('orchestra::users.edit', $data);
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

		// Check if provided id is the same as hidden id, just a pre-caution.
		if ((int) $id !== (int) $input['id']) 
		{
			return Response::error('500');
		}
		// Check if this is a new user, if so then password should always 
		// be required.
		elseif ((int) $id < 1)
		{
			$rules['password'] = array('required');
		}

		$this->fire_event('validate', array(& $rules));

		$val = Validator::make($input, $rules);
		$msg = Messages::make();

		if ($val->fails())
		{
			return Redirect::to(handles('orchestra::users/view/'.$id))
					->with_input()
					->with_errors($val);
		}

		$type = 'update';
		$user = User::find($id);

		if (is_null($user))
		{
			$type = 'create';
			$user = new User(array(
				'password' => $input['password'] ?: '',
				'status'   => User::UNVERIFIED,
			));
		}

		$user->fullname = $input['fullname'];
		$user->email    = $input['email'];

		if ( ! empty($input['password'])) $user->password = $input['password'];

		try
		{
			$this->fire_event(($type === 'create' ? 'creating' : 'updating'), array($user));
			$this->fire_event('saving', array($user));

			DB::transaction(function () use ($user, $input, $type)
			{
				$user->save();
				$user->roles()->sync($input['roles']);
			});

			$this->fire_event(($type === 'create' ? 'created' : 'updated'), array($user));
			$this->fire_event('saved', array($user));

			$msg->add('success', __("orchestra::response.users.{$type}"));
		}
		catch (Exception $e)
		{
			$msg->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
		}

		return Redirect::to(handles('orchestra::users'));
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
		if (is_null($id)) return Response::error('404');

		$user = User::find($id);
		$msg  = Messages::make();

		if (is_null($user) or ($user->id === Auth::user()->id))
		{
			return Response::error('404');
		}
		
		try
		{
			$this->fire_event('deleting', array($user));

			DB::transaction(function () use ($user)
			{				
				$user->roles()->delete();
				$user->delete();
			});

			$this->fire_event('deleted', array($user));

			$msg->add('success', __('orchestra::response.users.delete'));
		}
		catch (Exception $e)
		{
			$msg->add('error', __('orchestra::response.db-failed', array(
				'error' => $e->getMessage(),
			)));
		}

		return Redirect::to(handles('orchestra::users'));
	}

	/**
	 * Fire Event related to eloquent process
	 *
	 * @access private
	 * @param  string   $type
	 * @param  array    $parameters
	 * @return void
	 */
	private function fire_event($type, $parameters)
	{
		Event::fire("orchestra.{$type}: users", $parameters);
		Event::fire("orchestra.{$type}: user.account", $parameters);
	}
}