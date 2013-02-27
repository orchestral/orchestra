<?php namespace Orchestra\Tests\Models;

\Bundle::start('orchestra');

class UserTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();
		\Config::set('application.timezone', 'UTC');
	}

	/**
	 * Test Orchestra\Model\User constant.
	 *
	 * @test
	 * @group model
	 */
	public function testConfiguration()
	{
		$this->assertEquals(1, \Orchestra\Model\User::VERIFIED);
		$this->assertEquals(0, \Orchestra\Model\User::UNVERIFIED);
	}

	/**
	 * Test Orchestra\Model\User roles relationship.
	 *
	 * @test
	 * @group model
	 */
	public function testRolesRelationship()
	{
		$stub = \Orchestra\Model\User::with(array('roles', 'meta'))
					->where_id(1)
					->first();

		$this->assertInstanceOf('\Orchestra\Model\User', $stub);
		$this->assertTrue(is_array($stub->roles));
		$this->assertTrue(is_array($stub->meta));
		$this->assertInstanceOf('\Orchestra\Model\Role', $stub->roles[0]);
	}

	/**
	 * Test Orchestra\Model\User::localtime() method.
	 *
	 * @test
	 * @group model
	 */
	public function testTimezoneMethod()
	{
		\Orchestra\Model\User\Meta::create(array(
			'user_id' => 1,
			'name'    => 'timezone',
			'value'   => 'Asia/Kuala_Lumpur',
		));

		$user = \Orchestra\Model\User::find(1);

		$this->assertEquals('2012-01-01 08:00:00', 
			$user->localtime('2012-01-01 00:00:00')->format('Y-m-d H:i:s'));
		$this->assertEquals('Asia/Kuala_Lumpur', $user->timezone());
	}

	/**
	 * Test Orchestra\Model\User::search() method.
	 *
	 * @test
	 * @group model
	 */
	public function testSearchMethod()
	{
		$foo = \Orchestra\Model\User::search('invalid@dummy-user.com');
		$this->assertInstanceOf('\Laravel\Database\Eloquent\Query', $foo);
		$this->assertNull($foo->first());

		$user     = \Orchestra\Model\User::search('admin@orchestra.com', array(1))->first();
		$expected = \Orchestra\Model\User::find(1);
		$this->assertEquals($expected->id, $user->id);
	}
}