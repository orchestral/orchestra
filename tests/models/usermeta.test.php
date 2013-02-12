<?php

Bundle::start('orchestra');

class ModelUserMetaTest extends Orchestra\Testable\TestCase {

	/**
	 * Test Orchestra\Model\User\Meta configuration.
	 *
	 * @test
	 */
	public function testConfiguration()
	{
		$this->assertEquals('user_meta', Orchestra\Model\User\Meta::$table);
	}

	/**
	 * Test Orchestra\Model\User\Meta::users() relationship.
	 *
	 * @test
	 */
	public function testUsersRelationship()
	{
		$meta = Orchestra\Memory::make('user');
		$meta->put('timezone.2', 'Asia/Singapore');

		Orchestra\Core::shutdown();
		Orchestra\Core::start();

		$meta = Orchestra\Model\User\Meta::name('timezone', 2);
		$user = $meta->users()->first();

		$this->assertInstanceOf('Orchestra\Model\User', $user);

	}
}