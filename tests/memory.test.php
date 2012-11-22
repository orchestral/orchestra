<?php

class MemoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

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
