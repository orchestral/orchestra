<?php namespace Orchestra\Tests\Widget;

\Bundle::start('orchestra');

class WidgetPlaceholderTest extends \PHPUnit_Framework_TestCase {

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
		$this->stub = new \Orchestra\Widget\Placeholder('stub');
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
	 * @group core
	 * @group widget
	 */
	public function testInstanceOfPlaceholder()
	{
		$this->assertInstanceOf('\Orchestra\Widget\Placeholder', $this->stub);

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
	 * @group core
	 * @group widget
	 */
	public function testAddMethod()
	{
		$callback = function ()
		{
			return 'hello world';
		};

		$expected = array(
			'foo' => new \Orchestra\Support\Fluent(array(
				'value'  => $callback,
				'id'     => 'foo',
				'childs' => array(),
			)),
			'foobar' => new \Orchestra\Support\Fluent(array(
				'value'  => $callback,
				'id'     => 'foobar',
				'childs' => array(),
			)),
		);

		$this->stub->add('foo', $callback);
		$this->stub->add('foobar', 'after:foo', $callback);

		$this->assertEquals($expected, $this->stub->get());
	}
}
