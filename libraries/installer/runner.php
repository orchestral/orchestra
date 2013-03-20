<?php namespace Orchestra\Installer;

use \Bundle,
	\Config,
	\DB,
	\Event,
	\Exception,
	\Hash,
	\Input,
	\IoC,
	\Schema,
	\Session,
	\Str,
	\Validator,
	Orchestra\Installer as Installer,
	Orchestra\Messages,
	Orchestra\Model\User,
	Orchestra\Model\Role,
	Orchestra\Acl;

class Runner {

	/**
	 * Orchestra\Messages instance
	 *
	 * @static
	 * @access protected
	 * @var    Orchestra\Message
	 */
	protected static $message = null;

	/**
	 * Initiate Session/Message for Runner
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function initiate()
	{
		// avoid this method to from being called more than once.
		if ( ! is_null(static::$message)) return;

		// Fetch message from session, the message should be in serialize,
		// or else should return empty string.
		static::$message = Session::has('message') ? unserialize(Session::get('message', "")) : null;

		// check whether message actually an instanceof Messages, if for any
		// reason it's not we should assume the object is invalid and
		// therefore we need to construct a new object.
		if ( ! (static::$message instanceof Messages))
		{
			static::$message = Messages::make();
		}

		// Check if DEFAULT_BUNDLE has an instruction for Orchestra
		// installation, if so include it.
		if (is_file($file = path('app').'orchestra'.DS.'installer'.EXT))
		{
			include_once $file;
		}
	}

	/**
	 * Compile Message and save to Session
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function shutdown()
	{
		// Serialize and flash it to session.
		Session::flash('message', static::$message->serialize());
		static::$message = null;
	}

	/**
	 * Run all table installation
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function install()
	{
		static::initiate();

		// Run migration script to install `laravel_migrations` table to this
		// installation.
		if (IoC::registered('task: orchestra.migrator'))
		{
			IoC::resolve('task: orchestra.migrator', array('install'));
		}

		// We now need to run schema migration for Orchestra.
		static::install_options_schema();
		static::install_users_schema();

		Event::fire('orchestra.install.schema');

		static::shutdown();

		return true;
	}

	/**
	 * Create administrator account.
	 *
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function create_user($input)
	{
		if (Installer::installed()) return true;

		static::initiate();

		try
		{
			// Grab input fields and define the rules for user validations.
			$rules = array(
				'email'     => array('required', 'email'),
				'password'  => array('required'),
				'fullname'  => array('required'),
				'site_name' => array('required'),
			);

			$val = Validator::make($input, $rules);

			// Validate user registration, we should stop this process if
			// the user not properly formatted.
			if ($val->fails())
			{
				Session::flash('errors', $val->errors);
				return false;
			}

			// Before we create administrator, we should ensure that users
			// table is empty to avoid any possible hijack or invalid
			// request.
			$all = User::all();

			if ( ! empty($all))
			{
				throw new Exception('Unable to install when there already user registered');
			}

			// Create administator user
			$user = new User(array(
				'email'    => $input['email'],
				'password' => $input['password'],
				'fullname' => $input['fullname'],
				'status'   => 0,
			));

			Event::fire('orchestra.install: user', array($user, $input));

			$user->save();

			// Attach Administrator role to the newly created administrator
			// account.
			$user->roles()->insert(new Role(array('name' => 'Administrator')));

			// Make a new instance of Memory using provided IoC
			$memory = IoC::resolve('orchestra.memory');

			// Save the default application site_name.
			$memory->put('site.name', $input['site_name']);
			$memory->put('site.theme.backend', 'default');
			$memory->put('site.theme.frontend', 'default');
			$memory->put('email', Config::get('orchestra::email'));
			$memory->put('email.from', $input['email']);

			Role::create(array('name' => 'Member'));
			$actions = array('manage orchestra', 'manage users');

			// We should also create a basic ACL for Orchestra.
			$acl = Acl::make('orchestra');
			$acl->add_actions($actions);
			$acl->add_roles(array('Member', 'Administrator'));
			$acl->allow('Administrator', $actions);

			Event::fire('orchestra.install: acl', array($acl));

			$acl->attach($memory);

			// Installation is successful, we should be able to generate
			// success message to notify the user. Installer route will be
			// disabled after this point.
			static::$message->add('success', "User created, you can now login to the administation page");
		}
		catch (Exception $e)
		{
			static::$message->add('error', $e->getMessage());
		}

		static::shutdown();

		return true;
	}

	/**
	 * Install options table
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function install_options_schema()
	{
		try
		{
			// If this query does not return any Exception, we can assume
			// that users is already installed to current application
			DB::table('orchestra_options')->get();
		}
		catch (Exception $e)
		{
			Schema::table('orchestra_options', function ($table)
			{
				$table->create();

				$table->increments('id');

				$table->string('name', 64);
				$table->blob('value');

				$table->unique('name');
			});

			static::$message->add('success', 'Options table created');
		}
	}

	/**
	 * Install users and related tables
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function install_users_schema()
	{
		try
		{
			// If this query does not return any Exception, we can assume
			// that users is already installed to current application
			DB::table('users')->get();
		}
		catch (Exception $e)
		{
			Schema::table('users', function ($table)
			{
				$table->create();

				$table->increments('id');

				$table->string('email', 100);
				$table->string('password', 60);

				Event::fire('orchestra.install.schema: users', array($table));

				$table->string('fullname', 100)->nullable();
				$table->integer('status')->nullable();

				// add timestamp created_at and updated_at
				$table->timestamps();

				// ensure email address is unique
				$table->unique('email');
			});

			Schema::table('user_meta', function ($table)
			{
				$table->create();

				$table->increments('id');
				$table->integer('user_id')->unsigned();
				$table->string('name', 255)->nullable();
				$table->text('value')->nullable();

				// add timestamp created_at and updated_at
				$table->timestamps();

				// ensure email address is unique
				$table->index('user_id');
				$table->index(array('user_id', 'name'));
			});

			Schema::table('roles', function ($table)
			{
				$table->create();

				$table->increments('id');
				$table->string('name', 255);

				// add timestamp created_at and updated_at
				$table->timestamps();
			});

			Schema::table('user_roles', function ($table)
			{
				$table->create();

				$table->increments('id');
				$table->integer('user_id')->unsigned();
				$table->integer('role_id')->unsigned();

				// add timestamp created_at and updated_at
				$table->timestamps();

				$table->index(array('user_id', 'role_id'));
			});

			static::$message->add('success', 'Users table created');
		}
	}
}
