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
		Orchestra\Core::shutdown();

		unset($this->user);
		unset($this->stub);

		parent::tearDown();
	}

	/**
	 * Test instance of Orchestra\Repository\User
	 *
	 * @test
	 */
	public function testInstanceOfUserRepository()
	{
		$this->assertInstanceOf('Hybrid\Memory\Driver', $this->stub);
		$this->assertInstanceOf('Hybrid\Memory\Driver', new Orchestra\Repository\User);
	
		$refl    = new \ReflectionObject($this->stub);
		$storage = $refl->getProperty('storage');
		$key_map = $refl->getProperty('key_map');

		$storage->setAccessible(true);
		$key_map->setAccessible(true);

		$this->assertEquals('usermeta', $storage->getValue($this->stub));
		$this->assertTrue(is_array($key_map->getValue($this->stub)));
	}

	/**
	 * Test Orchestra\Repository\User::get()
	 *
	 * @test
	 */
	public function testGetMethod()
	{
		$this->stub->put("foo.1", "foobar");
		$this->stub->put("timezone.1", "UTC");
		$this->stub->put("age.1", 20);

		$this->assertEquals('foobar', $this->stub->get("foo.1"));
		$this->assertNull( $this->stub->get("foobar.1"));

		$refl = new \ReflectionObject($this->stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$this->assertEquals(array(
			'foo/user-1'      => 'foobar',
			'timezone/user-1' => 'UTC',
			'age/user-1'      => 20,
			'foobar/user-1'   => null,
		), $data->getValue($this->stub));

		$this->stub->forget('foobar.1');

		$this->assertEquals(array(
			'foo/user-1'      => 'foobar',
			'timezone/user-1' => 'UTC',
			'age/user-1'      => 20,
			'foobar/user-1'   => null,
		), $data->getValue($this->stub));

		$this->stub->shutdown();

		$stub = new Orchestra\Repository\User;
		$this->assertEquals('foobar', $stub->get('foo.1'));
		$stub->put('foo.1', 'hello foobar');
		$stub->forget('age.1');

		$stub->shutdown();
	}

	/**
	 * Test Orchestra\Repository\User::put()
	 *
	 * @test
	 */
	public function testPutMethod()
	{
		$stub = new Orchestra\Repository\User;
		$stub->put("hello.1", "Hello World");

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$this->assertEquals(array('hello/user-1' => 'Hello World'), $data->getValue($stub));
		$this->assertEquals('Hello World', $stub->get("hello.1"));
	}
}