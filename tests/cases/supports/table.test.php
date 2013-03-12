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
		$stub = new \Orchestra\Support\Table(function ($t) { });
		
		$refl = new \ReflectionObject($stub);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Table', $stub);
		
		$this->assertNull($name->getValue($stub));
		$this->assertNull($stub->name);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid->getValue($stub));
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $stub->grid);
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
		$mock_data = array(
			new \Laravel\Fluent(array('id' => 1, 'name' => 'Laravel')),
			new \Laravel\Fluent(array('id' => 2, 'name' => 'Illuminate')),
			new \Laravel\Fluent(array('id' => 3, 'name' => 'Symfony')),
		);

		$mock1 = new \Orchestra\Support\Table(function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attributes(array('class' => 'foo'));

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

		$mock2 = new \Orchestra\Support\Table(function ($t) use ($mock_data)
		{
			$t->rows($mock_data);
			$t->attributes = array('class' => 'foo');

			$t->column('ID', 'id');
			$t->column('name', function ($c)
			{
				$c->value(function ($row)
				{
					return '<strong>'.$row->name.'</strong>';
				});
			});
		});

		ob_start();
		echo $mock1;
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<table class="table table-bordered table-striped foo">
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

		$expected = '<table class="table table-bordered table-striped foo">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>&lt;strong&gt;Laravel&lt;/strong&gt;</td>
		</tr>
		<tr>
			<td>2</td>
			<td>&lt;strong&gt;Illuminate&lt;/strong&gt;</td>
		</tr>
		<tr>
			<td>3</td>
			<td>&lt;strong&gt;Symfony&lt;/strong&gt;</td>
		</tr>
	</tbody>
</table>
';

		$this->assertEquals($expected, $mock2->render());
	}
}
