<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class ExpressionTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Laravel\Expression 
	 *
	 * @test
	 * @group support
	 */
	public function testConstructReturnValid()
	{
		$expected = "foobar";
		$actual   = new \Orchestra\Support\Expression($expected);

		$this->assertInstanceOf('\Orchestra\Support\Expression', $actual);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($expected, $actual->get());
	}
}
