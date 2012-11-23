<?php

class WidgetNestyTest extends PHPUnit_Framework_TestCase {

	/**
	 * Orchestra\Widget\Nesty instance.
	 *
	 * @var Orchestra\Widget\Nesty
	 */
	private $nesty = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
		$this->nesty = new Orchestra\Widget\Nesty(array());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->nesty);
	}

	/**
	 * Test instance of $this->nesty
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Orchestra\Widget\Nesty', $this->nesty);
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
	 * Test adding an item to nest
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

		$this->nesty->add('foo');
		$this->nesty->add('hello', 'before:foo');
		$this->nesty->add('world', 'after:hello');
		$this->nesty->add('bar', 'childof:foo');
		$this->nesty->add('foobar', 'child_of:foo');

		$this->assertEquals($expected, $this->nesty->get());
	}
}
