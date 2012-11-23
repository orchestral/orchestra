<?php

class WidgetPlaceholderTest extends PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Widget\Pane
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
		$this->stub = new Orchestra\Widget\Placeholder('stub');
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
	public function testInstanceOf()
	{
		$this->assertInstanceOf('Orchestra\Widget\Placeholder', $this->stub);
	}

	/**
	 * Test add an item return properly.
	 *
	 * @test
	 */
	public function testAddItemIsReturnProperly()
	{
		$expected = array(
			'foo' => new Laravel\Fluent(array(
				'value'  => '',
				'id'     => 'foo',
				'childs' => array(),
			)),
		);

		$this->stub->add('foo');

		$this->assertEquals($expected, $this->stub->get());
	}
}
