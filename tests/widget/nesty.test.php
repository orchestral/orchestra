<?php

class WidgetNestyTest extends PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Widget\Nesty
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
		$this->stub = new Orchestra\Widget\Nesty(array());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
	 */
	public function testInstanceOfStub()
	{
		$this->assertInstanceOf('Orchestra\Widget\Nesty', $this->stub);
	}

	/**
	 * Get newly instantiated Orchestra\Widget\Nesty::get() return empty
	 * string.
	 *
	 * @test
	 */
	public function testNewInstantiatedInstanceReturnEmptyArray()
	{
		$this->assertEquals(array(),
			with(new Orchestra\Widget\Nesty(array()))->get());
	}

	/**
	 * Test adding an item to Orchestra\Widget\Nesty.
	 *
	 * @test
	 */
	public function testAddItemIsProperlyReturned()
	{
		$expected = array(
			'hello' => new Laravel\Fluent(array(
				'id'     => 'hello',
				'childs' => array(),
			)),
			'world' => new Laravel\Fluent(array(
				'id'     => 'world',
				'childs' => array(),
			)),
			'foo' => new Laravel\Fluent(array(
				'id'     => 'foo',
				'childs' => array(
					'bar' => new Laravel\Fluent(array(
						'id'     => 'bar',
						'childs' => array(),
					)),
					'foobar' => new Laravel\Fluent(array(
						'id'     => 'foobar',
						'childs' => array(),
					)),
				),
			))
		);

		$this->stub->add('foo');
		$this->stub->add('hello', 'before:foo');
		$this->stub->add('world', 'after:hello');
		$this->stub->add('bar', 'childof:foo');
		$this->stub->add('foobar', 'child_of:foo');

		$this->assertEquals($expected, $this->stub->get());
	}
}
