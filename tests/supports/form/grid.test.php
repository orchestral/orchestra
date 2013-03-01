<?php namespace Orchestra\Tests\Supports\Form;

\Bundle::start('orchestra');

class GridTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\Support\Form\Grid.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfGrid()
	{
		$stub = new \Orchestra\Support\Form\Grid(array(
			'submit_button' => 'Submit',
			'view'          => 'foo',
		));

		$refl          = new \ReflectionObject($stub);
		$submit_button = $refl->getProperty('submit_button');
		$view          = $refl->getProperty('view');

		$submit_button->setAccessible(true);
		$view->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $stub);
		$this->assertEquals('Submit', $submit_button->getValue($stub));
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Support\Form\Grid::row() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRowMethod()
	{
		$mock = new \Laravel\Fluent;
		$stub = new \Orchestra\Support\Form\Grid(array());
		$stub->row($mock);

		$refl = new \ReflectionObject($stub);
		$row  = $refl->getProperty('row');
		$row->setAccessible(true);

		$this->assertEquals($mock, $row->getValue($stub));
		$this->assertTrue(isset($stub->row));
	}

	/**
	 * Test Orchestra\Support\Form\Grid::layout() method.
	 *
	 * @test
	 * @group support
	 */
	public function testLayoutMethod()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$refl = new \ReflectionObject($stub);
		$view = $refl->getProperty('view');
		$view->setAccessible(true);

		$stub->layout('horizontal');
		$this->assertEquals('orchestra::support.form.horizontal', $view->getValue($stub));

		$stub->layout('vertical');
		$this->assertEquals('orchestra::support.form.vertical', $view->getValue($stub));

		$stub->layout('foo');
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Support\Form\Grid::attributes() method.
	 *
	 * @test
	 * @group support
	 */
	public function testAttributesMethod()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$refl   = new \ReflectionObject($stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$stub->attributes(array('class' => 'foo'));

		$this->assertEquals(array('class' => 'foo'), $attributes->getValue($stub));
		$this->assertEquals(array('class' => 'foo'), $stub->attributes());

		$stub->attributes('id', 'foobar');

		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $attributes->getValue($stub));
		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $stub->attributes());
	}

	/**
	 * Test Orchestra\Support\Form\Grid::fieldset() method.
	 *
	 * @test
	 * @group support
	 */
	public function testFieldsetMethod()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$refl      = new \ReflectionObject($stub);
		$fieldsets = $refl->getProperty('fieldsets');
		$fieldsets->setAccessible(true);

		$this->assertEquals(array(), $fieldsets->getValue($stub));

		$stub->fieldset('Foobar', function ($f) {});
		$stub->fieldset(function ($f) {});

		$fieldset = $fieldsets->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Support\Form\Fieldset', 
			$fieldset[0]);
		$this->assertEquals('Foobar', 
			$fieldset[0]->name);
		$this->assertInstanceOf('\Orchestra\Support\Form\Fieldset', 
			$fieldset[1]);
		$this->assertNull($fieldset[1]->name);
	}

	/**
	 * Test Orchestra\Support\Form\Grid::hidden() method.
	 *
	 * @test
	 * @group support
	 */
	public function testHiddenMethod()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());
		$stub->row(new \Laravel\Fluent(array(
			'foo'    => 'foobar',
			'foobar' => 'foo',
		)));

		$stub->hidden('foo');
		$stub->hidden('foobar', function ($f)
		{
			$f->value('stubbed');
		});

		$refl    = new \ReflectionObject($stub);
		$hiddens = $refl->getProperty('hiddens'); 
		$hiddens->setAccessible(true);

		$data = $hiddens->getValue($stub);

		$this->assertEquals(\Form::hidden('foo', 'foobar'), $data['foo']);
		$this->assertEquals(\Form::hidden('foobar', 'stubbed'), $data['foobar']);
	}

	/**
	 * Test Orchestra\Support\Form\Grid magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$stub->invalid_method();
	}

	/**
	 * Test Orchestra\Support\Form\Grid magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$invalid = $stub->invalid_property;
	}

	/**
	 * Test Orchestra\Support\Form\Grid magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$stub->invalid_property = array('foo');
	}

	/**
	 * Test Orchestra\Support\Form\Grid magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$stub->attributes = 'foo';
	}

	/**
	 * Test Orchestra\Support\Form\Grid magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Grid(array());

		$invalid = isset($stub->invalid_property) ? true : false;
	}
}