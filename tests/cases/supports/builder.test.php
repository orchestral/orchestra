<?php namespace Orchestra\Tests\Supports;

class BuilderTest extends \PHPUnit_Framework_TestCase {

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
		$this->stub = new BuilderStub(function ($t) {});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}
	
	/**
	 * Test Instance of Orchestra\Support\Table.
	 *
	 * @test
	 * @group support
	 */	
	public function testInstanceOfTable()
	{
		$stub1 = new BuilderStub(function ($t) { });
		$stub2 = BuilderStub::make(function ($t) { });
		
		$refl = new \ReflectionObject($stub1);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Builder', $stub1);
		$this->assertInstanceOf('\Orchestra\Support\Builder', $stub2);

		$this->assertNull($name->getValue($stub1));
		$this->assertNull($stub1->name);
		$this->assertInstanceOf('\Orchestra\Tests\Supports\GridStub', $grid->getValue($stub1));
		$this->assertInstanceOf('\Orchestra\Tests\Supports\GridStub', $stub1->grid);
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
	 * test Orchestra\Support\Builder::render() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRenderMethod()
	{
		$mock1 = new BuilderStub(function ($t) {});
		$mock2 = new BuilderStub(function ($t) {});

		ob_start();
		echo $mock1;
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo', $output);
		$this->assertEquals('foo', $mock2->render());
	}
}

class GridStub {}

class BuilderStub extends \Orchestra\Support\Builder {

	public function __construct(\Closure $callback)
	{
		$this->grid = new GridStub;
	}

	public function render()
	{
		return 'foo';
	}
}
