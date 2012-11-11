<?php

class TableTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test Orchestra\Table is an instance of Hybrid\Table
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$table = Orchestra\Table::make(function () {});
		$this->assertInstanceOf('Hybrid\Table', $table);
	}
}