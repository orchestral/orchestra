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
		$this->assertInstanceOf('Hybrid\Memory\Driver', new Orchestra\Repository\User);
	}

	/**
	 * Test Orchestra\Repository\User::get()
	 *
	 * @test
	 */
	public function testRepositoryUserGet()
	{
		Orchestra\Model\User\Meta::create(array(
			'name'    => 'foo',
			'value'   => 'foobar',
			'user_id' => 1,
		));

		$foo = $this->stub->get("foo.1");

		$this->assertEquals('foobar', $foo->value);

		$foo = $this->stub->get("foobar.1");

		$this->assertTrue(is_null($foo->value));

		$refl = new \ReflectionObject($this->stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		
	}
}