<?php

Bundle::start('orchestra');

class WidgetTest extends PHPUnit_Framework_TestCase {
	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Widget\Driver
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new WidgetStub('foobar', array());
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test Orchestra\Widget::make()
	 *
	 * @test
	 */
	public function testMakeReturnProperInstanceOf()
	{
		$this->assertInstanceOf('Orchestra\Widget\Menu',
			Orchestra\Widget::make('menu'));
		$this->assertInstanceOf('Orchestra\Widget\Pane',
			Orchestra\Widget::make('pane'));
		$this->assertInstanceOf('Orchestra\Widget\Placeholder',
			Orchestra\Widget::make('placeholder'));
	}

	/**
	 * Test Orchestra\Widget::make() with different name return different
	 * instance.
	 *
	 * @test
	 */
	public function testMakeDifferentNameReturnDifferentInstance()
	{
		$this->assertNotEquals(Orchestra\Widget::make('menu.a'),
			Orchestra\Widget::make('menu.b'));
	}

	/**
	 * Test Orchestra\Widget::make() with the same name return the same
	 * instance.
	 *
	 * @test
	 */
	public function testMakeSameNameReturnSameInstance()
	{
		$this->assertEquals(Orchestra\Widget::make('menu.a'),
			Orchestra\Widget::make('menu.a'));
	}

	/**
	 * Test Orchestra\Widget::make() with an invalid driver throw an
	 * exception
	 *
	 * @expectedException \Exception
	 */
	public function testMakeWithInvalidDriverThrowException()
	{
		Orchestra\Widget::make('menus');
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
	 */
	public function testInstanceOfStub()
	{
		$this->assertInstanceOf('Orchestra\Widget\Driver', $this->stub);
	}

	/**
	 * Test Orchestra\Widget\Driver::render() stub return as expected.
	 *
	 * @test
	 */
	public function testRenderStub()
	{
		$this->assertEquals('stub', $this->stub->render());
	}

	/**
	 * Test add an item using stub.
	 */
	public function testAddItemUsingStubReturnProperly()
	{
		$expected = array(
			'foo' => new Laravel\Fluent(array(
				'id'     => 'foo',
				'title'  => 'foobar',
				'foobar' => true,
				'childs' => array(),
			)),
		);

		$this->stub->add('foo')->title('foobar');
		$this->assertEquals($expected, $this->stub->get());
	}
}

class WidgetStub extends Orchestra\Widget\Driver {

	protected $type = 'stub';
	protected $config = array(
		'defaults' => array(
			'title'   => '',
			'foobar'  => true,
		),
	);

	public function render()
	{
		return $this->type;
	}

	public function add($id, $location = 'parent', $callback = null)
	{
		$item = $this->nesty->add($id, $location ?: 'parent');

		if ($callback instanceof Closure)
		{
			call_user_func($callback, $item);
		}

		return $item;
	}
}
