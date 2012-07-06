<?php namespace Orchestra\Installer;

use \Config, \DB, \Exception, \Hash, \Input, \Schema, \Session, \Str, \Validator, 
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
		if ( ! is_null(static::$message)) return;

		static::$message = Session::has('message') ? unserialize(Session::get('message', "")) : null;

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

		static::install_migrations();
		static::install_options();
		static::install_users();

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

		try {
			$all = User::all();

			if ( ! empty($all))
			{
				throw new Exception('Unable to install when there already user registered');
			}

			$input = Input::all();
			$rules = array(
				'email'    => array('required', 'email'),
				'password' => array('required'),
				'fullname' => array('required'),
			);

			$v = Validator::make($input, $rules);

			if ($v->fails())
			{
				Session::flash('errors', $v->errors);
				return false;
			}

			$user = User::create(array(
				'email'    => $input['email'],
				'password' => Hash::make($input['password']),
				'fullname' => $input['fullname'],
				'status'   => 0,
			));

			$user->roles()->insert(new Role(array('name' => 'Administrator')));

			// memory module
			$memory = Memory::make('fluent.orchestra_options');

			$memory->put('site_name', Input::get('site_name', 'Orchestra'));

			// build basic acl
			$acl = Acl::make('orchestra');
			$acl->attach($memory);

			$actions = array(
				'manage orchestra',
				'manage users',
			);

			$acl->add_role('Administrator');
			$acl->add_actions($actions);

			$acl->allow('Administrator', $actions);
			

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
	protected static function install_migrations()
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

				$table->primary(array('extension', 'name'));
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
	protected static function install_options()
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

				$table->string('name', 255);
				$table->text('value');

				// add timestamp created_at and updated_at
				$table->timestamps();

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
	protected static function install_users()
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
				$table->integer('user_id');
				$table->string('key', 255)->nullable();
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
				$table->integer('user_id');
				$table->integer('role_id');

				// add timestamp created_at and updated_at
				$table->timestamps();
			});

			static::$message->add('success', 'Users table created');
		}
	}
}