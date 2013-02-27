<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class AclTest extends \Orchestra\Testable\TestCase {

	/**
	 * Role instance.
	 *
	 * @var Orchestra\Model\Role
	 */
	protected $role = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->role = \Orchestra\Model\Role::create(array(
			'name' => 'foo',
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->role);

		parent::tearDown();
	}

	/**
	 * Test instanceof Orchestra\Acl
	 *
	 * @test
	 * @group core
	 */
	public function testInstanceOfAcl()
	{
		$acl = \Orchestra\Acl::make();
		$this->assertInstanceOf('\Orchestra\Support\Acl\Container', $acl);
	}

	/**
	 * Test model create CRUD for Orchestra\Model\Role does sync with Orchestra\Acl
	 *
	 * @test
	 * @group core
	 */
	public function testModelCreateRoleSyncToAcl()
	{
		$acl   = \Orchestra\Core::acl();
		
		$refl  = new \ReflectionObject($acl);
		$roles = $refl->getProperty('roles');
		$roles->setAccessible(true);

		$this->assertTrue($acl->has_role($this->role->name));
		$this->assertEquals(array('guest', 'member', 'administrator', 'foo'),
			$roles->getValue($acl)->get());
	}

	/**
	 * Test model update CRUD for Orchestra\Model\Role does sync with Orchestra\Acl
	 *
	 * @test
	 * @group core
	 */
	public function testModelUpdateRoleSyncToAcl()
	{
		$acl   = \Orchestra\Core::acl();
		$refl  = new \ReflectionObject($acl);
		$roles = $refl->getProperty('roles');
		$roles->setAccessible(true);

		$this->role->name = 'foobar';
		$this->role->save();

		$this->assertTrue($acl->has_role($this->role->name));
		$this->assertEquals(array('guest', 'member', 'administrator', 'foobar'),
			$roles->getValue($acl)->get());

		$this->role->name = 'foo';
		$this->role->save();
	}

	/**
	 * Test model delete CRUD for Orchestra\Model\Role does sync with Orchestra\Acl
	 *
	 * @test
	 * @group core
	 */
	public function testModelDeleteRoleSyncToAcl()
	{
		$acl   = \Orchestra\Core::acl();
		$refl  = new \ReflectionObject($acl);
		$roles = $refl->getProperty('roles');
		$roles->setAccessible(true);

		$editor = \Orchestra\Model\Role::create(array('name' => 'editor'));

		$this->assertTrue($acl->has_role($editor->name));
		$this->assertEquals(array('guest', 'member', 'administrator', 'foo', 'editor'),
			$roles->getValue($acl)->get());

		$this->role->delete();

		$this->assertFalse($acl->has_role('foo'));
		$this->assertEquals(array('guest', 'member', 'administrator', 4 => 'editor'),
			$roles->getValue($acl)->get());
	}
}