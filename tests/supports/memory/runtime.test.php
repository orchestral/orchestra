<?php namespace Hybrid\Tests\Memory;

\Bundle::start('hybrid');

class RuntimeTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Hybrid\Memory\Runtime
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new \Hybrid\Memory\Runtime('stub', array());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test Hybrid\Memory\Runtime::__construct()
	 *
	 * @test
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
	 * Test Hybrid\Memory\Runtime::initiate()
	 *
	 * @test
	 */
	public function testInitiateMethod()
	{
		$this->assertTrue($this->stub->initiate());
	}

	/**
	 * Test Hybrid\Memory\Runtime::shutdown()
	 *
	 * @test
	 */
	public function testShutdownMethod()
	{
		$this->assertTrue($this->stub->shutdown());
	}
}