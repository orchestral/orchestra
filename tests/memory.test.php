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
		$memory = Orchestra\Memory::make('runtime.orchestra-memory');
		$this->assertInstanceOf('Hybrid\Memory\Driver', $memory);
	}
}
