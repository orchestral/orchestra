<?php namespace Orchestra\Installer;

use \Config, \DB, \Exception, \Hash, \Input, \IoC, 
	\Schema, \Session, \Str, \Validator, 
	Orchestra\Installer as Installer,
	Orchestra\Messages,
	Orchestra\Model\User,
	Orchestra\Model\Role,
	Hybrid\Acl,
	Hybrid\Memory;

class Runner
{
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

		// Fetch message from session, the message should be in serialize, or else should return empty string.
		static::$message = Session::has('message') ? unserialize(Session::get('message', "")) : null;

		// check whether message actually an instanceof Messages, if for any reason it's not we should
		// assume the object is invalid and therefore we need to construct a new object.
		if ( ! (static::$message instanceof Messages)) static::$message = new Messages;
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

		// Run migration script to install `laravel_migrations` table
		// to this installation.
		if (IoC::registered('task: orchestra.migrator'))
		{
			IoC::resolve('task: orchestra.migrator', array('install'));
		}

		// We now need to run schema migration for Orchestra. 
		static::install_migrations_schema();
		static::install_options_schema();
		static::install_users_schema();

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
	public static function create_user()
	{
		if ( ! $_POST or Installer::installed()) return true;

		static::initiate();

		try 
		{
			// Grab input fields and define the rules for user validations.
			$input = Input::all();
			$rules = array(
				'email'    => array('required', 'email'),
				'password' => array('required'),
				'fullname' => array('required'),
			);

			$v = Validator::make($input, $rules);

			// Validate user registration, we should stop this process
			// if the user not properly formatted.
			if ($v->fails())
			{
				Session::flash('errors', $v->errors);
				return false;
			}


			// Before we create administrator, we should ensure that users 
			// table is empty to avoid any possible hijack or invalid request.
			$all = User::all();

			if ( ! empty($all))
			{
				throw new Exception('Unable to install when there already user registered');
			}

			// Create administator user
			$user = User::create(array(
				'email'    => $input['email'],
				'password' => Hash::make($input['password']),
				'fullname' => $input['fullname'],
				'status'   => 0,
			));

			// Attach Administrator role to the newly created administrator 
			// account.
			$user->roles()->insert(new Role(array('name' => 'Administrator')));

			// Make a new instance of Memory using `orchestra_options` table.
			$memory = Memory::make('fluent.orchestra_options');

			// Save the default application site_name.
			$memory->put('site.name', Input::get('site_name', 'Orchestra'));
			$memory->put('email', Config::get('orchestra::email'));
			$memory->put('email.from', $input['email']);

			// We should also create a basic ACL for Orchestra.
			$acl = Acl::make('orchestra');

			// attach memory instance, this allow the acl to be saved to 
			// database
			$acl->attach($memory);

			$actions = array(
				'manage orchestra',
				'manage users',
			);

			$acl->add_role('Administrator');
			$acl->add_actions($actions);

			$acl->allow('Administrator', $actions);

			Role::create(array(
				'name' => 'Member'
			));

			$acl->add_role('Member');

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
	 * Install migrations table
	 *
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function install_migrations_schema()
	{
		try 
		{
			// If this query does not return any Exception, we can assume
			// that migrations is already installed to current application
			DB::table('orchestra_migrations')->get();
		}
		catch (Exception $e)
		{
			Schema::table('orchestra_migrations', function ($table)
			{
				$table->create();

				// Migrations can be run for a specific extension, so we'll use
				// the core name and string migration name as an unique ID
				// for the migrations, allowing us to easily identify which
				// migrations have been run for each bundle.
				$table->string('extension', 50);

				$table->string('name', 200);

				// When running a migration command, we will store a batch
				// ID with each of the rows on the table. This will allow
				// us to grab all of the migrations that were run for the
				// last command when performing rollbacks.
				$table->integer('batch');
			});

			static::$message->add('success', 'Migration table created');
		}
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
				$table->string('fullname', 100)->nullable();
				
				$table->string('password', 60);
				$table->integer('status');

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
			});

			static::$message->add('success', 'Users table created');
		}
	}
}