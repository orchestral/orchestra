<?php

Bundle::start('orchestra');

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

		$refl   = new \ReflectionObject($this->stub);
		$type   = $refl->getProperty('type');
		$config = $refl->getProperty('config');

		$type->setAccessible(true);
		$config->setAccessible(true);

		$this->assertEquals(array('defaults' => array('value' => '')), $config->getValue($this->stub));
		$this->assertEquals('placeholder', $type->getValue($this->stub));
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
			'foobar' => new Laravel\Fluent(array(
				'value'  => 'hello world',
				'id'     => 'foobar',
				'childs' => array(),
			)),
		);

		$this->stub->add('foo');
		$this->stub->add('foobar', 'after:foo', function ()
		{
			return 'hello world';
		});

		$this->assertEquals($expected, $this->stub->get());
	}
}
