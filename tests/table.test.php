<?php

Bundle::start('orchestra');

class TableTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Table is an instance of Hybrid\Table
	 *
	 * @test
	 */
	public function testInstanceOfTable()
	{
		$table = Orchestra\Table::make(function () {});
		$refl  = new \ReflectionObject($table);
		$grid  = $refl->getProperty('grid');

		$grid->setAccessible(true);

		$this->assertInstanceOf('Hybrid\Table', $table);
		$this->assertInstanceOf('Hybrid\Table\Grid', $grid->getValue($table));
	}
}