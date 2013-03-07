<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class FormTest extends \PHPUnit_Framework_TestCase {

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
		$this->stub = \Orchestra\Support\Form::of('stub', function ($t) {});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		\Orchestra\Support\Form::$names = array();
	}
	
	/**
	 * Test Instance of Orchestra\Support\Form.
	 *
	 * @test
	 * @group support
	 */	
	public function testInstanceOfForm()
	{
		$stub = new \Orchestra\Support\Form(function ($t) { });
		
		$refl = new \ReflectionObject($stub);
		$name = $refl->getProperty('name');
		$grid = $refl->getProperty('grid');
		
		$name->setAccessible(true);
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Form', $stub);
		
		$this->assertNull($name->getValue($stub));
		$this->assertNull($stub->name);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid->getValue($stub));
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $stub->grid);
	}

	/**
	 * test Orchestra\Support\Form::render()
	 *
	 * @test
	 * @group support
	 */
	public function testRenderMethod()
	{
		$mock_data = new \Laravel\Fluent(array(
			'id' => 1, 
			'name' => 'Laravel'
		));

		$mock1 = new \Orchestra\Support\Form(function ($form) use ($mock_data)
		{
			$form->row($mock_data);
			$form->attributes(array(
				'method' => 'POST',
				'action' => 'http://localhost',
				'class'  => 'foo',
			));
		});

		$mock2 = new \Orchestra\Support\Form(function ($form) use ($mock_data)
		{
			$form->row($mock_data);
			$form->attributes = array(
				'method' => 'POST',
				'action' => 'http://localhost',
				'class'  => 'foo'
			);
		});

		ob_start();
		echo $mock1;
		$output = ob_get_contents();
		ob_end_clean();

		$expected = '<form class="form-horizontal" method="POST" action="http://localhost" accept-charset="UTF-8">
<div class="form-actions">
	<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>';

		$this->assertEquals($expected, $output);

		$expected = '<form class="form-horizontal" method="POST" action="http://localhost" accept-charset="UTF-8">
<div class="form-actions">
	<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>';

		$this->assertEquals($expected, $mock2->render());
	}
}