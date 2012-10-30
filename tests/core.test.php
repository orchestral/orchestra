<?php

class TestCore extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test Orchestra\Core::start()
	 *
	 * @test
	 */
	public function testStartingUpOrchestra()
	{
		Orchestra\Core::start();

		$memory = Orchestra\Core::memory();
		$menu   = Orchestra\Core::menu();

		$this->assertNotNull($memory);
		$this->assertInstanceOf('Hybrid\Memory\Driver', $memory);

		$this->assertNotNull($menu);
		$this->assertInstanceOf('Orchestra\Widget\Menu', $menu);
	}
}