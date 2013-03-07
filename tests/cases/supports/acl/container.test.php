<?php namespace Orchestra\Tests\Supports\Acl;

\Bundle::start('orchestra');

class ContainerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Acl Container instance.
	 *
	 * @var Orchestra\Support\Acl\Container
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$runtime = \Orchestra\Support\Memory::make('runtime.foo');
		$runtime->put('acl_foo', static::providerMemory());

		$this->stub = \Orchestra\Support\Acl::make('foo', $runtime);

		\Event::override('orchestra.auth: roles', function ($user_id, $roles)
		{
			return array('guest');
		});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Add data provider
	 * 
	 * @return array
	 */
	public static function providerMemory()
	{
		return array(
			'acl'     => array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true),
			'actions' => array('Manage User', 'Manage'),
			'roles'   => array('Guest', 'Admin'),
		);
	}

	/**
	 * Test instance of stub.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfStub()
	{
		$refl    = new \ReflectionObject($this->stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Acl\Container', 
			$this->stub);
		$this->assertInstanceOf('\Orchestra\Support\Memory\Runtime', 
			$memory->getValue($this->stub));
		$this->assertInstanceOf('\Orchestra\Support\Acl\Fluent', 
			$roles->getValue($this->stub));
		$this->assertInstanceOf('\Orchestra\Support\Acl\Fluent', 
			$actions->getValue($this->stub));
		$this->assertTrue(is_array($acl->getValue($this->stub)));
	}

	/**
	 * Test sync memory.
	 *
	 * @test
	 * @group support
	 */
	public function testSyncMemoryAfterConstruct()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new \Orchestra\Support\Acl\Container('foo');

		$this->assertFalse($stub->attached());

		$stub->attach($runtime);

		$this->assertTrue($stub->attached());

		$stub->add_role('foo');
		$stub->add_action('foobar');
		$stub->allow('foo', 'foobar');

		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');
		$acl     = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$memory->getValue($stub)->get('acl_foo.roles'));
		$this->assertEquals(array('guest', 'admin', 'foo'), 
			$runtime->get('acl_foo.roles'));

		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$memory->getValue($stub)->get('acl_foo.actions'));
		$this->assertEquals(array('manage-user', 'manage', 'foobar'), 
			$runtime->get('acl_foo.actions'));

		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => true, '1:1' => true, '2:2' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Support\Acl\Container::attach() method throws exception
	 * when attaching multiple memory instance.
	 *
	 * @expectedException \RuntimeException
	 * @group support
	 */
	public function testAttachMethodThrowsExceptionWhenAttachMultipleMemory()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new \Orchestra\Support\Acl\Container('foo', $runtime);
		$stub->attach($runtime);
	}

	/**
	 * Test Orchestra\Support\Acl\Container::allow() method.
	 *
	 * @test
	 * @group support
	 */
	public function testAllowMethod()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new \Orchestra\Support\Acl\Container('foo', $runtime);

		$stub->allow('guest', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => true, '0:1' => false, '1:0' => true, '1:1' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Support\Acl\Container::deny() method.
	 *
	 * @test
	 * @group support
	 */
	public function testDenyMethod()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub    = new \Orchestra\Support\Acl\Container('foo', $runtime);

		$stub->deny('admin', 'manage-user');

		$refl   = new \ReflectionObject($this->stub);
		$memory = $refl->getProperty('memory');
		$acl    = $refl->getProperty('acl');

		$memory->setAccessible(true);
		$acl->setAccessible(true);

		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$acl->getValue($stub));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$memory->getValue($stub)->get('acl_foo.acl'));
		$this->assertEquals(array('0:0' => false, '0:1' => false, '1:0' => false, '1:1' => true), 
			$runtime->get('acl_foo.acl'));
	}

	/**
	 * Test Orchestra\Support\Acl\Container::can() method.
	 *
	 * @test
	 * @group support
	 */
	public function testCanMethod()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new \Orchestra\Support\Acl\Container('foo', $runtime);

		$stub->add_actions(array('Manage Page', 'Manage Photo'));
		$stub->allow('guest', 'Manage Page');

		$this->assertFalse($stub->can('manage'));
		$this->assertTrue($stub->can('manage-page'));
		$this->assertFalse($stub->can('manage-photo'));
	}

	/**
	 * Test Orchestra\Support\Acl\Container::check() method.
	 *
	 * @test
	 * @group support
	 */
	public function testCheckMethod()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory());

		$stub = new \Orchestra\Support\Acl\Container('foo', $runtime);

		$stub->add_actions(array('Manage Page', 'Manage Photo'));
		$stub->allow('guest', 'Manage Page');

		$this->assertFalse($stub->check('guest', 'manage'));
		$this->assertTrue($stub->check('guest', 'manage-page'));
		$this->assertFalse($stub->check('guest', 'manage-photo'));
	}

	/**
	 * Test Orchestra\Support\Acl\Container::check() method throws exception.
	 *
	 * @expectedException InvalidArgumentException
	 * @group support
	 */
	public function testCheckMethodUsingMockOneThrowsException()
	{
		$this->stub->check('guest', 'view foo');
	}

	/**
	 * Test Orchestra\Support\Acl\Container::allow() method throws exception
	 * for roles.
	 *
	 * @expectedException InvalidArgumentException
	 * @group support
	 */
	public function testAllowMethodUsingMockOneThrowsExceptionForRoles()
	{
		$this->stub->allow('boss', 'view blog');
	}

	/**
	 * Test Orchestra\Support\Acl\Container::allow() method throws exception
	 * for actions.
	 *
	 * @expectedException InvalidArgumentException
	 * @group support
	 */
	public function testAllowMethodUsingMockOneThrowsExceptionForActions()
	{
		$this->stub->allow('guest', 'view foo');
	}

	/**
	 * Test memory is properly sync during construct.
	 *
	 * @test
	 * @group support
	 */
	public function testMemoryIsProperlySync()
	{
		$stub    = $this->stub;
		$refl    = new \ReflectionObject($stub);
		$memory  = $refl->getProperty('memory');
		$roles   = $refl->getProperty('roles');
		$actions = $refl->getProperty('actions');

		$memory->setAccessible(true);
		$roles->setAccessible(true);
		$actions->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Memory\Runtime', $memory->getValue($stub));

		$this->assertInstanceOf('\Orchestra\Support\Acl\Fluent', $roles->getValue($stub));

		$this->assertTrue($stub->roles()->has('guest'));
		$this->assertTrue($stub->roles()->has('admin'));
		$this->assertTrue($stub->has_role('guest'));
		$this->assertTrue($stub->has_role('admin'));
		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertInstanceOf('\Orchestra\Support\Acl\Fluent', $actions->getValue($stub));

		$this->assertTrue($stub->actions()->has('manage-user'));
		$this->assertTrue($stub->actions()->has('manage'));
		$this->assertTrue($stub->has_action('manage-user'));
		$this->assertTrue($stub->has_action('manage'));
		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}

	/**
	 * Test adding duplicate roles and actions is properly handled
	 *
	 * @test
	 * @group support
	 */
	public function testAddDuplicates()
	{
		$runtime = new \Orchestra\Support\Memory\Runtime('foo');
		$runtime->put('acl_foo', static::providerMemory()); 

		$stub    = new \Orchestra\Support\Acl\Container('foo', $runtime);
		$refl    = new \ReflectionObject($stub);
		$actions = $refl->getProperty('actions');
		$roles   = $refl->getProperty('roles');

		$actions->setAccessible(true);
		$roles->setAccessible(true);

		$stub->roles()->add('admin');
		$stub->roles()->fill(array('admin'));
		$stub->add_role('admin');
		$stub->add_roles(array('admin'));

		$stub->actions()->add('manage');
		$stub->actions()->fill(array('manage'));
		$stub->add_action('manage');
		$stub->add_actions(array('manage'));

		$this->assertEquals(array('guest', 'admin'), $roles->getValue($stub)->get());
		$this->assertEquals(array('guest', 'admin'), $stub->roles()->get());

		$this->assertEquals(array('manage-user', 'manage'), $actions->getValue($stub)->get());
		$this->assertEquals(array('manage-user', 'manage'), $stub->actions()->get());
	}
}