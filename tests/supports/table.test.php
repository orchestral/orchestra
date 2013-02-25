<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class TableTest extends \PHPUnit_Framework_TestCase {

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
		$this->stub = \Orchestra\Support\Table::of('stub', function ($t) {});

		$mock_data = array(
			new \Laravel\Fluent(array('id' => 1, 'name' => 'Laravel')),
			new \Laravel\Fluent(array('id' => 2, 'name' => 'Illuminate')),
			new \Laravel\Fluent(array('id' => 3, 'name' => 'Symfony')),
		);

		\Orchestra\Support\Table::of('mock-1', function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attr(array('class' => 'foo'));

			$t->column('id');
			$t->column(function ($c) 
			{
				$c->id = 'name';
				$c->label('Name');
				$c->value(function ($row)
				{
					return $row->name;
				});
			});
		});

		\Orchestra\Support\Table::of('mock-2', function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attr = array('class' => 'foo');

			$t->column('ID', 'id');
			$t->column('name', function ($c)
			{
				$c->value(function ($row)
				{
					return '<strong>'.$row->name.'</strong>';
				});
			});
		});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		\Orchestra\Support\Table::$names = array();
	}
	
	/**
	 * Test Instance of Orchestra\Support\Table.
	 *
	 * @test
	 * @group support
	 */	
	public function testInstanceOfTable()
	{
		$stub1 = new \Orchestra\Support\Table(function ($t) { });
		$stub2 = \Orchestra\Support\Table::make(function ($t) { });
		
		$refl = new \ReflectionObject($stub1);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Table', $stub1);
		$this->assertInstanceOf('\Orchestra\Support\Table', $stub2);

		$this->assertNull($name->getValue($stub1));
		$this->assertNull($stub1->name);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid->getValue($stub1));
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $stub1->grid);
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
	 * Test Orchestra\Support\Table::of() method.
	 *
	 * @test
	 * @group support
	 */
	public function testTableOfMethod()
	{
		$this->assertEquals(\Orchestra\Support\Table::of('stub'), $this->stub);
		$this->assertEquals('stub', $this->stub->name);
	}
	
	/**
	 * test Orchestra\Support\Table::render() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRenderMethod()
	{
		ob_start();
		echo \Orchestra\Support\Table::of('mock-1');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<table class="foo">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>Laravel</td>
		</tr>
		<tr>
			<td>2</td>
			<td>Illuminate</td>
		</tr>
		<tr>
			<td>3</td>
			<td>Symfony</td>
		</tr>
	</tbody>
</table>
';

		$this->assertEquals($expected, $output);

		ob_start();
		echo \Orchestra\Support\Table::of('mock-2');
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<table class="foo">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td><strong>Laravel</strong></td>
		</tr>
		<tr>
			<td>2</td>
			<td><strong>Illuminate</strong></td>
		</tr>
		<tr>
			<td>3</td>
			<td><strong>Symfony</strong></td>
		</tr>
	</tbody>
</table>
';

		$this->assertEquals($expected, $output);
	}
}
