<?php namespace Orchestra\Tests\Supports\Site;

class DecoratorTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Support\Site\Decorator::$macros = array();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Support\Site\Decorator::$macros = array();
	}

	/**
	 * Test add and using macros.
	 *
	 * @test
	 * @group support
	 */
	public function testAddAndUsingMacros()
	{
		\Orchestra\Support\Site\Decorator::macro('foo', function ()
		{
			return 'foo';
		});

		$this->assertEquals('foo', \Orchestra\Support\Site\Decorator::foo());
	}

	/**
	 * Test calling undefined macros throws an exception.
	 *
	 * @expectedException \BadMethodCallException
	 * @group support
	 */
	public function testCallingUndefinedMacrosThrowsException()
	{
		\Orchestra\Support\Site\Decorator::foobar();
	}
}