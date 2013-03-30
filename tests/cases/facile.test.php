<?php namespace Orchestra\Tests;

class FacileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Facile::$templates = array(
			'default' => \IoC::resolve('\Orchestra\Facile\Template'),
		);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Facile::$templates = array();
	}

	/**
	 * Test Orchestra\Facile::make() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testMakeMethod()
	{
		$stub = \Orchestra\Facile::make('default', array('view' => 'error.404'), 'html');

		$this->assertInstanceOf('\Orchestra\Facile', $stub);
	}

	/**
	 * Test Orchestra\Facile::make() throws exception when using an invalid 
	 * template.
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testMakeMethodThrowsExceptionUsingInvalidTemplate()
	{
		\Orchestra\Facile::make('foo', array('view' => 'error.404'), 'html');
	}

	/**
	 * Test Orchestra\Facile::template() method
	 *
	 * @test
	 * @group facile
	 */
	public function testTemplateMethod()
	{
		\Orchestra\Facile::template('foo', '\Orchestra\Tests\ValidTemplateStub');

		$this->assertInstanceOf('\Orchestra\Tests\ValidTemplateStub', 
			\Orchestra\Facile::$templates['foo']);
	}

	/**
	 * Test Orchestra\Facile::template() method throws exception when 
	 * template isn't an instance of Orchestra\Facile\Driver.
	 *
	 * @group facile
	 * @expectedException \RuntimeException
	 */
	public function testTemplateMethodThrowsException()
	{
		\Orchestra\Facile::template('foobar', '\Orchestra\Tests\InvalidTemplateStub');
	}
}

class ValidTemplateStub extends \Orchestra\Facile\Driver {}

class InvalidTemplateStub {}
