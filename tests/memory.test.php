<?php

Bundle::start('orchestra');

class MemoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test instance of Hybrid\Memory.
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$memory  = Orchestra\Memory::make('runtime.orchestra-memory');
		$refl    = new \ReflectionObject($memory);
		$name    = $refl->getProperty('name');
		$storage = $refl->getProperty('storage');
		
		$name->setAccessible(true);
		$storage->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Memory\Driver', $memory);
		$this->assertEquals('runtime', $storage->getValue($memory));
		$this->assertEquals('orchestra-memory', $name->getValue($memory));
	}
}
