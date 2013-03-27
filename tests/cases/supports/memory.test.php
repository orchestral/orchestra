<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class MemoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Config::set('application.profiler', false);

		\Orchestra\Support\Memory::extend('stub', function($driver, $config) 
		{
			return new MemoryStub($driver, $config);
		});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\File::delete(path('storage').'cache/orchestra.memory.default');
	}

	/**
	 * Test that Orchestra\Support\Memory::make() return an instanceof Orchestra\Support\Memory.
	 * 
	 * @test
	 * @group support
	 */
	public function testMake()
	{
		$this->assertInstanceOf('\Orchestra\Support\Memory\Runtime', 
			\Orchestra\Support\Memory::make('runtime')); 
		$this->assertInstanceOf('\Orchestra\Support\Memory\Cache', 
			\Orchestra\Support\Memory::make('cache')); 
	}

	/**
	 * Test that Orchestra\Support\Memory::make() return exception when given invalid driver
	 *
	 * @expectedException \Exception
	 * @group support
	 */
	public function testMakeExpectedException()
	{
		\Orchestra\Support\Memory::make('orm');
	}

	/**
	 * Test Orchestra\Support\Memory::extend() return valid Memory instance.
	 *
	 * @test
	 * @group support
	 */
	public function testStubMemory()
	{
		$stub = \Orchestra\Support\Memory::make('stub.mock');

		$this->assertInstanceOf('\Orchestra\Tests\Supports\MemoryStub', $stub);

		$refl    = new \ReflectionObject($stub);
		$storage = $refl->getProperty('storage');
		$storage->setAccessible(true);

		$this->assertEquals('stub', $storage->getValue($stub));
	}

	/**
	 * Test Orchestra\Support\Memory::__construct() method.
	 *
	 * @expectedException \RuntimeException
	 * @group support
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Support\Memory;
	}

	/**
	 * Test Orchestra\Support\Memory::shutdown() method.
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$stub = \Orchestra\Support\Memory::make();
		$this->assertTrue($stub === \Orchestra\Support\Memory::make());

		\Event::fire('laravel.done', array(''));

		$this->assertFalse($stub === \Orchestra\Support\Memory::make());
	}
}

class MemoryStub extends \Orchestra\Support\Memory\Driver
{
	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'stub';

	/**
	 * No initialize method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() {}

	/**
	 * No shutdown method for runtime
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() {}
}