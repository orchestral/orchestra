<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class WidgetTest extends \PHPUnit_Framework_TestCase {
	
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

		\Orchestra\Widget::extend('stub', function ($name, $config)
		{
			return new WidgetStub($name, $config);
		});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test Orchestra\Widget::make() method.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testInstanceOfWidget()
	{
		$this->assertInstanceOf('\Orchestra\Widget\Menu',
			\Orchestra\Widget::make('menu'));
		$this->assertInstanceOf('\Orchestra\Widget\Pane',
			\Orchestra\Widget::make('pane'));
		$this->assertInstanceOf('\Orchestra\Widget\Placeholder',
			\Orchestra\Widget::make('placeholder'));
	}

	/**
	 * Test Orchestra\Memory::__construct() throws an exception.
	 *
	 * @expectedException \RuntimeException
	 * @group core
	 * @group widget
	 */
	public function testConstructThrowsException()
	{
		$stub = new \Orchestra\Widget;
	}

	/**
	 * Test Orchestra\Widget::make() with different name return different
	 * instance.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testMakeDifferentNameReturnDifferentInstance()
	{
		$this->assertNotEquals(\Orchestra\Widget::make('menu.a'),
			\Orchestra\Widget::make('menu.b'));
	}

	/**
	 * Test Orchestra\Widget::make() with the same name return the same
	 * instance.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testMakeSameNameReturnSameInstance()
	{
		$this->assertEquals(\Orchestra\Widget::make('menu.a'),
			\Orchestra\Widget::make('menu.a'));
	}

	/**
	 * Test Orchestra\Widget::make() with an invalid driver throw an
	 * exception
	 *
	 * @expectedException \Exception
	 * @group core
	 * @group widget
	 */
	public function testMakeWithInvalidDriverThrowsException()
	{
		\Orchestra\Widget::make('menus');
	}

	/**
	 * Test instanceof WidgetStub.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testInstanceOfStub()
	{
		$this->assertInstanceOf('\Orchestra\Widget\Driver', 
			$this->stub);
		$this->assertInstanceOf('\Orchestra\Widget\Driver', 
			\Orchestra\Widget::make('stub'));
		$this->assertInstanceOf('\Orchestra\Tests\WidgetStub', 
			\Orchestra\Widget::make('stub'));

		$stub     = new WidgetStub('foobar', array());
		$expected = array(
			'defaults' => array(
				'title'   => '',
				'foobar'  => true,
			),
		);

		$refl   = new \ReflectionObject($stub);
		$config = $refl->getProperty('config');
		$name   = $refl->getProperty('name');
		$nesty  = $refl->getProperty('nesty');

		$config->setAccessible(true);
		$name->setAccessible(true);
		$nesty->setAccessible(true);

		$this->assertEquals($expected, $config->getValue($stub));
		$this->assertEquals('foobar', $name->getValue($stub));
		$this->assertInstanceOf('\Orchestra\Widget\Nesty', $nesty->getValue($stub));
	}

	/**
	 * Test add an item using stub is valid.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testAddItemUsingStubIsValid()
	{
		$stub     = \Orchestra\Widget::make('stub');
		$expected = array(
			'foo' => new \Orchestra\Support\Fluent(array(
				'id'     => 'foo',
				'title'  => 'foobar',
				'foobar' => false,
				'childs' => array(),
			)),
			'foobar' => new \Orchestra\Support\Fluent(array(
				'id'     => 'foobar',
				'title'  => 'hello world',
				'foobar' => true,
				'childs' => array(),
			)),
		);

		$stub->add('foobar', 'parent', function ($item)
		{
			$item->title = 'hello world';
		});

		$stub->add('foo', 'before:foobar', function ($item)
		{
			$item->foobar = false;
		})->title('foobar');

		$this->assertEquals($expected, $stub->get());
		$this->assertEquals($expected, $stub->items);
	}

	/**
	 * Test Orchestra\Widget\Driver::__get() throws an exception
	 *
	 * @expectedException \InvalidArgumentException
	 * @group core
	 * @group widget
	 */
	public function testAccessGetThrowsException()
	{
		$stub  = new WidgetStub('foo', array());
		$hello = $stub->hello;
	}
}

class WidgetStub extends \Orchestra\Widget\Driver {

	protected $type   = 'stub';
	protected $config = array(
		'defaults' => array(
			'title'   => '',
			'foobar'  => true,
		),
	);

	public function add($id, $location = 'parent', $callback = null)
	{
		$item = $this->nesty->add($id, $location ?: 'parent');

		if ($callback instanceof \Closure)
		{
			call_user_func($callback, $item);
		}

		return $item;
	}
}

