<?php

Bundle::start('orchestra');

class ExtensionPublisherTest extends Orchestra\Testable\TestCase {

	/**
	 * Stub instance.
	 *
	 * @var PublisherStub
	 */
	private $stub = null;

	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	private $user = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->stub = new PublisherStub;
		$this->user = Orchestra\Model\User::find(1);

		$this->be($this->user);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		unset($this->user);

		$this->be(null);

		parent::tearDown();
	}

	/**
	 * Test instance of Orchestra\Extension\Publisher default driver
	 *
	 * @test
	 */
	public function testInstanceOfDefaultDriver()
	{
		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'ftp');

		$this->assertInstanceOf('Orchestra\Extension\Publisher\Driver',	
			Orchestra\Extension\Publisher::driver());
		$this->assertInstanceOf('Orchestra\Extension\Publisher\FTP',	
			Orchestra\Extension\Publisher::driver());
	}

	/**
	 * Test instance of Orchestra\Extension\Publisher using stub.
	 *
	 * @test
	 */
	public function testInstanceOfPublisher()
	{
		$this->assertInstanceOf('Orchestra\Extension\Publisher\Driver',	
			$this->stub);

		$this->assertTrue($this->stub->upload(DEFAULT_BUNDLE));
		$this->assertTrue($this->stub->connected());
	}
}

class PublisherStub extends Orchestra\Extension\Publisher\Driver {
	/**
	 * Get service connection instance.
	 *
	 * @access public
	 * @return Object
	 */
	public function connection()
	{
		return $this;
	}

	/**
	 * Connect to the service.
	 *
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public function connect($config = array())
	{
		// connect to foo.... connected.
	}

	/**
	 * Upload the file.
	 *
	 * @access public
	 * @param  string   $name   Extension name
	 * @return bool
	 */
	public function upload($name)
	{
		return true;
	}

	/**
	 * Verify that the driver is connected to a service.
	 *
	 * @access public
	 * @return bool
	 */
	public function connected()
	{
		return true;
	}
}