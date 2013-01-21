<?php

Bundle::start('orchestra');

class RepositoryUserTest extends Orchestra\Testable\TestCase {
	
	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	protected $user = null;

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Repository\User
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->user = Orchestra\Model\User::find(1);
		$this->stub = Orchestra\Memory::make('user');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		unset($this->stub);

		parent::tearDown();
	}

	/**
	 * Test instance of Orchestra\Repository\User
	 *
	 * @test
	 */
	public function testInstanceOfStub()
	{
		$this->assertInstanceOf('Hybrid\Memory\Driver', $this->stub);
		$this->assertInstanceOf('Hybrid\Memory\Driver', new Orchestra\Respository\User);
	}
}