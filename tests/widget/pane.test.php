<?php

class WidgetPaneTest extends PHPUnit_Framework_TestCase {

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
		$this->stub = new Orchestra\Widget\Pane('stub');
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
		$this->assertInstanceOf('Orchestra\Widget\Pane', $this->stub);
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
				'attr'    => array(),
				'title'   => '',
				'content' => '',
				'html'    => '',
				'id'      => 'foo',
				'childs'  => array(),
			)),
		);

		$this->stub->add('foo');

		$this->assertEquals($expected, $this->stub->get());
	}
}
