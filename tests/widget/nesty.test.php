<?php namespace Orchestra\Tests\Widget;

\Bundle::start('orchestra');

class NestyTest extends \PHPUnit_Framework_TestCase {

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
		$this->stub = new \Orchestra\Widget\Nesty(array());
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
	public function testInstanceOfNesty()
	{
		$this->assertInstanceOf('\Orchestra\Widget\Nesty', $this->stub);

		$refl   = new \ReflectionObject($this->stub);
		$config = $refl->getProperty('config');
		$config->setAccessible(true);

		$this->assertEquals(array(), $config->getValue($this->stub));
	}

	/**
	 * Get newly instantiated Orchestra\Widget\Nesty::get() return empty
	 * string.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testNewInstanceReturnEmptyArray()
	{
		$this->assertEquals(array(),
			with(new \Orchestra\Widget\Nesty(array()))->get());
	}

	/**
	 * Test adding an item to Orchestra\Widget\Nesty.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testAddMethod()
	{
		$expected = array(
			'hello' => new \Orchestra\Support\Fluent(array(
				'id'     => 'hello',
				'childs' => array(),
			)),
			'world' => new \Orchestra\Support\Fluent(array(
				'id'     => 'world',
				'childs' => array(),
			)),
			'foo' => new \Orchestra\Support\Fluent(array(
				'id'     => 'foo',
				'childs' => array(
					'bar' => new \Orchestra\Support\Fluent(array(
						'id'     => 'bar',
						'childs' => array(),
					)),
					'foobar' => new \Orchestra\Support\Fluent(array(
						'id'     => 'foobar',
						'childs' => array(
							'hello-foobar' => new \Orchestra\Support\Fluent(array(
								'id'     => 'hello-foobar',
								'childs' => array(),
							)),
						),
					)),
					'foo-bar' => new \Orchestra\Support\Fluent(array(
						'id'     => 'foo-bar',
						'childs' => array(),
					)),
					'hello-world-foobar' => new \Orchestra\Support\Fluent(array(
						'id'     => 'hello-world-foobar',
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
		$this->stub->add('foo-bar', 'child-of:foo');
		$this->stub->add('hello-foobar', 'child-of:foo.foobar');
		$this->stub->add('hello-world-foobar', 'child-of:foo.dummy');

		$this->assertEquals($expected, $this->stub->get());
	}
}
