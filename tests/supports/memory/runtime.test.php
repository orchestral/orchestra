<?php namespace Orchestra\Tests\Supports\Memory;

\Bundle::start('orchestra');

class RuntimeTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Support\Memory\Runtime
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new \Orchestra\Support\Memory\Runtime('stub', array());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test Orchestra\Support\Memory\Runtime::__construct()
	 *
	 * @test
	 * @group support
	 */
	public function testConstructMethod()
	{
		$refl    = new \ReflectionObject($this->stub);
		$name    = $refl->getProperty('name');
		$storage = $refl->getProperty('storage');

		$name->setAccessible(true);
		$storage->setAccessible(true);

		$this->assertEquals('runtime', $storage->getValue($this->stub));
		$this->assertEquals('stub', $name->getValue($this->stub));
	}

	/**
	 * Test Orchestra\Support\Memory\Runtime::initiate()
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$this->assertTrue($this->stub->initiate());
	}

	/**
	 * Test Orchestra\Support\Memory\Runtime::shutdown()
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$this->assertTrue($this->stub->shutdown());
	}
}