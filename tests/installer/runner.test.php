<?php namespace Orchestra\Tests\Installer;

\Bundle::start('orchestra');

class RunnerTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$_SERVER['orchestra.install.schema-users'] = null;
		$_SERVER['orchestra.install.schema']       = null;
		$_SERVER['orchestra.install.user']         = null;
		$_SERVER['orchestra.install.acl']          = null;

		\Event::listen('orchestra.install.schema: users', function()
		{
			$_SERVER['orchestra.install.schema-users'] = 'foo';
		});

		\Event::listen('orchestra.install.schema', function()
		{
			$_SERVER['orchestra.install.schema'] = 'foo';
		});

		\Event::listen('orchestra.install: user', function()
		{
			$_SERVER['orchestra.install.user'] = 'foo';
		});

		\Event::listen('orchestra.install: acl', function()
		{
			$_SERVER['orchestra.install.acl'] = 'foo';
		});

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);

		parent::setUp();
		
		\Orchestra\Installer::$status = false;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($_SERVER['orchestra.install.schema-users']);
		unset($_SERVER['orchestra.install.schema']);
		unset($_SERVER['orchestra.install.user']);
		unset($_SERVER['orchestra.install.acl']);

		set_path('app', path('base').'application'.DS);

		parent::tearDown();
	}

	/**
	 * Test Installation generate proper configuration
	 *
	 * @test
	 * @group installer
	 */
	public function testInstallationGenerateProperConfiguration()
	{
		$this->restartApplication();

		$this->assertTrue(\Orchestra\Installer::installed());

		$memory = \Orchestra\Core::memory();

		$this->assertInstanceOf('\Orchestra\Support\Memory\Fluent', $memory);
		$this->assertEquals('Orchestra Test Suite', $memory->get('site.name'));
		$this->assertEquals('', $memory->get('site.description'));
		$this->assertEquals('default', $memory->get('site.theme.frontend'));
		$this->assertEquals('default', $memory->get('site.theme.backend'));

		$this->assertEquals('mail', $memory->get('email.default'));
		$this->assertEquals('admin@orchestra.com', $memory->get('email.from'));
	}

	/**
	 * Test administrator user is properly created.
	 *
	 * @test
	 * @group installer
	 */
	public function testAdministratorUserIsCreated()
	{
		$this->restartApplication();

		$user = \Orchestra\Model\User::find(1);
		$this->assertEquals('Test Administrator', $user->fullname);
		$this->assertEquals('admin@orchestra.com', $user->email);

		// Test login the administrator.
		if (\Auth::attempt(array('username' => 'admin@orchestra.com', 'password' => '123456')))
		{
			$this->assertTrue(true, 'Able to authenticate');

			$acl = \Orchestra\Core::acl();

			$this->assertInstanceOf('\Orchestra\Support\Acl\Container', $acl);

			if ($acl->can('manage-orchestra'))
			{
				$this->assertTrue(true, 'Able to manage orchestra');
			}
			else
			{
				$this->assertTrue(false, 'Unable to manage orchestra');
			}
		}
		else
		{
			$this->assertTrue(false, 'If unable to authenticate');
		}

		\Auth::logout();
	}

	/**
	 * Test all events is properly fired during installation.
	 *
	 * @test
	 * @group installer
	 */
	public function testInstallationEventIsFired()
	{
		$lists = array(
			'orchestra.install.schema-users',
			'orchestra.install.schema',
			'orchestra.install.user',
			'orchestra.install.acl',
		);

		foreach ($lists as $list)
		{
			$this->assertEquals('foo', $_SERVER[$list]);
		}
	}
}
