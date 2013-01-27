<?php

Bundle::start('orchestra');

class ModelsUserTest extends Orchestra\Testable\TestCase {
	
	/**
	 * Test Orchestra\Model\User constant.
	 *
	 * @test
	 */
	public function testEloquentConstant()
	{
		$this->assertEquals(1, Orchestra\Model\User::VERIFIED);
		$this->assertEquals(0, Orchestra\Model\User::UNVERIFIED);
	}

	/**
	 * Test Orchestra\Model\User roles relationship.
	 *
	 * @test
	 */
	public function testRolesRelationship()
	{
		$stub = Orchestra\Model\User::with('roles')->where_id(1)->first();

		$this->assertInstanceOf('Orchestra\Model\User', $stub);
		$this->assertTrue(is_array($stub->roles));
		$this->assertInstanceOf('Orchestra\Model\Role', $stub->roles[0]);
	}
}