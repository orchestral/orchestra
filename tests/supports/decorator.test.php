<?php namespace Orchestra\Tests\Supports;

class DecoratorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Support\Table
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = DecoratorStub::of('stub', function ($t) {});
		DecoratorStub::of('mock-1', function ($t) {});
		DecoratorStub::of('mock-2', function ($t) {});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		DecoratorStub::$names = array();
	}
	
	/**
	 * Test Instance of Orchestra\Support\Table.
	 *
	 * @test
	 * @group support
	 */	
	public function testInstanceOfTable()
	{
		$stub1 = new DecoratorStub(function ($t) { });
		$stub2 = DecoratorStub::make(function ($t) { });
		
		$refl = new \ReflectionObject($stub1);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Decorator', $stub1);
		$this->assertInstanceOf('\Orchestra\Support\Decorator', $stub2);

		$this->assertNull($name->getValue($stub1));
		$this->assertNull($stub1->name);
		$this->assertInstanceOf('\Orchestra\Tests\Supports\DecoratorGrid', $grid->getValue($stub1));
		$this->assertInstanceOf('\Orchestra\Tests\Supports\DecoratorGrid', $stub1->grid);
	}

	/**
	 * Test Orchestra\Support\Table::__get throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodGetThrowsException()
	{
		$expected = $this->stub->expected;
	}

	/**
	 * Test Orchestra\Support\Decorator::of() method.
	 *
	 * @test
	 * @group support
	 */
	public function testTableOfMethod()
	{
		$this->assertEquals(DecoratorStub::of('stub'), $this->stub);
		$this->assertEquals('stub', $this->stub->name);
	}
	
	/**
	 * test Orchestra\Support\Decorator::render() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRenderMethod()
	{
		ob_start();
		echo DecoratorStub::of('mock-1');
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo', $output);
		$this->assertEquals('foo', DecoratorStub::of('mock-2')->render());
	}
}

class DecoratorGrid {}

class DecoratorStub extends \Orchestra\Support\Decorator {

	public function __construct(\Closure $callback)
	{
		$this->grid = new DecoratorGrid;
	}

	public function render()
	{
		return 'foo';
	}
}