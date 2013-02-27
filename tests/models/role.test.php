<?php namespace Orchestra\Tests\Models;

\Bundle::start('orchestra');

class RoleTest extends \Orchestra\Testable\TestCase {
	
	/**
	 * Test Orchestra\Model\Role::admin() method.
	 *
	 * @test
	 * @group model
	 */
	public function testAdminMethod()
	{
		$admin = \Orchestra\Model\Role::admin();
		$this->assertEquals(\Config::get('orchestra::orchestra.default_role'), 
			$admin->id);
	}

	/**
	 * Test Orchestra\Model\Role::member() method.
	 *
	 * @test
	 * @group model
	 */
	public function testMemberMethod()
	{
		$member = \Orchestra\Model\Role::member();
		$this->assertEquals(\Config::get('orchestra::orchestra.member_role'), 
			$member->id);
	}

	/**
	 * Test Orchestra\Model\Role::users() relationship method.
	 *
	 * @test
	 * @group model
	 */
	public function testUsersRelationship()
	{
		$admin    = \Orchestra\Model\Role::admin();
		$user     = $admin->users()->first();
		$expected = \Orchestra\Model\User::find(1);

		$this->assertEquals($expected->id, $user->id);
	}
}