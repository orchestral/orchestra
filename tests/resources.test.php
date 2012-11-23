<?php

class ResourcesTest extends PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var  Orchestra\Resources
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');

		$this->stub = Orchestra\Resources::make('stub', array(
			'name' => 'ResourceStub',
			'uses' => 'stub',
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test Orchestra\Resources::make().
	 *
	 * @test
	 */
	public function testMakeAResource()
	{
		$this->assertInstanceOf('Orchestra\Resources', $this->stub);
		$this->assertEquals('ResourceStub', $this->stub->name);
		$this->assertEquals('stub', $this->stub->uses);

		$foo = Orchestra\Resources::make('foo', array(
			'name' => 'Foobar',
			'uses' => 'foo',
		));

		$this->assertInstanceOf('Orchestra\Resources', $foo);
		$this->assertEquals('Foobar', $foo->name);
		$this->assertEquals('foo', $foo->uses);
	}
}
