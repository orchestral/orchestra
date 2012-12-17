<?php

Bundle::start('orchestra');

class AclTest extends Orchestra\Testable\TestCase {

	/**
	 * Test instanceof Orchestra\Acl
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$acl = Orchestra\Acl::make();
		$this->assertInstanceOf('Hybrid\Acl\Container', $acl);
	}

	/**
	 * Test adding new role is attached to acl
	 *
	 * @test
	 */
	public function testModelRoleIsSyncWithAcl()
	{
		$foo = new Orchestra\Model\Role(array('name' => 'foo'));
		$foo->save();

		$acl = Orchestra\Core::acl();

		$this->assertTrue($acl->has_role($foo->name));

		$refl = new \ReflectionObject($acl);
		$roles = $refl->getProperty('roles');
		$roles->setAccessible(true);

		$this->assertEquals(array('guest', 'member', 'administrator', 'foo'),
			$roles->getValue($acl)->get());

		$foo->name = 'foobar';
		$foo->save();

		$this->assertTrue($acl->has_role($foo->name));
		$this->assertEquals(array('guest', 'member', 'administrator', 'foobar'),
			$roles->getValue($acl)->get());

		$editor = Orchestra\Model\Role::create(array('name' => 'editor'));

		$this->assertTrue($acl->has_role($editor->name));
		$this->assertEquals(array('guest', 'member', 'administrator', 'foobar', 'editor'),
			$roles->getValue($acl)->get());

		$foo->delete();

		$this->assertFalse($acl->has_role('foobar'));
		$this->assertEquals(array('guest', 'member', 'administrator', 4 => 'editor'),
			$roles->getValue($acl)->get());
	}
}
