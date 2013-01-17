<?php

Bundle::start('orchestra');

class TableTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Table is an instance of Hybrid\Table
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$table = Orchestra\Table::make(function () {});
		$this->assertInstanceOf('Hybrid\Table', $table);

		$refl = new \ReflectionObject($table);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Table\Grid', $grid->getValue($table));
	}
}