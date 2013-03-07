<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class TableTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Table is an instance of Orchestra\Support\Table
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfTable()
	{
		$table = \Orchestra\Table::make(function () {});
		$refl  = new \ReflectionObject($table);
		$grid  = $refl->getProperty('grid');

		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Table', $table);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid->getValue($table));
	}
}