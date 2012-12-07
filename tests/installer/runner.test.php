<?php

Bundle::start('orchestra');

class InstallerRunnerTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$_SESSION['orchestra.installation'] = array();

		Orchestra\Installer::$status = false;
	}

	/**
	 * Test Installation generate proper configuration
	 *
	 * @test
	 */
	public function testInstallationGenerateProperConfiguration()
	{
		$this->createApplication();

		$this->assertTrue(Orchestra\Installer::installed());

		$memory = Orchestra\Core::memory();

		$this->assertInstanceOf('Hybrid\Memory\Fluent', $memory);
		$this->assertEquals('Orchestra', $memory->get('site.name'));
		$this->assertEquals('', $memory->get('site.description'));
		$this->assertEquals('default', $memory->get('site.theme.frontend'));
		$this->assertEquals('default', $memory->get('site.theme.backend'));

		$this->assertEquals('mail', $memory->get('email.default'));
		$this->assertEquals('example@test.com', $memory->get('email.from'));
	}

	/**
	 * Test administrator user is properly created.
	 *
	 * @test
	 */
	public function testAdministratorUserProperlyCreated()
	{
		$this->createApplication();

		$user = Orchestra\Model\User::find(1);
		$this->assertEquals('Orchestra TestRunner', $user->fullname);
		$this->assertEquals('example@test.com', $user->email);

		// Test login the administrator.
		if (Auth::attempt(array('username' => 'example@test.com', 'password' => '123456')))
		{
			$this->assertTrue(true, 'Able to authenticate');

			$acl = Orchestra\Core::acl();

			$this->assertInstanceOf('Hybrid\Acl', $acl);

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

	}

	/**
	 * Test all events is properly fired during installation.
	 *
	 * @test
	 */
	public function testEventProperlyFired()
	{
		Event::listen('orchestra.install.schema: users', function()
		{
			$_SESSION['orchestra.installation'][] = 'orchestra.install.schema: users';
		});

		Event::listen('orchestra.install.schema', function()
		{
			$_SESSION['orchestra.installation'][] = 'orchestra.install.schema';
		});

		Event::listen('orchestra.install: user', function()
		{
			$_SESSION['orchestra.installation'][] = 'orchestra.install: user';
		});

		Event::listen('orchestra.install: acl', function()
		{
			$_SESSION['orchestra.installation'][] = 'orchestra.install: acl';
		});

		$this->createApplication();

		$expected = array(
			'orchestra.install.schema: users',
			'orchestra.install.schema',
			'orchestra.install: user',
			'orchestra.install: acl',
		);

		$this->assertEquals($expected, $_SESSION['orchestra.installation']);
	}
}
