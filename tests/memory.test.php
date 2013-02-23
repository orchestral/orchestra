<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class MemoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instance of Hybrid\Memory.
	 *
	 * @test
	 * @group core
	 */
	public function testInstanceOfMemory()
	{
		$memory  = \Orchestra\Memory::make('runtime.orchestra-memory');
		$refl    = new \ReflectionObject($memory);
		$name    = $refl->getProperty('name');
		$storage = $refl->getProperty('storage');
		
		$name->setAccessible(true);
		$storage->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Memory\Driver', $memory);
		$this->assertEquals('runtime', $storage->getValue($memory));
		$this->assertEquals('orchestra-memory', $name->getValue($memory));
	}
}
