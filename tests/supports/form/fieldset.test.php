<?php namespace Orchestra\Tests\Supports\Form;

\Bundle::start('orchestra');

class FieldsetTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Session::$instance = null;

		// load the session.
		\Session::load();

		\Config::set('orchestra::support.form.fieldset', array(
			'select'   => array(),
			'textarea' => array(),
			'input'    => array(),
			'password' => array(),
			'radio'    => array(),
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Session::$instance = null;
	}

	/**
	 * Test instance of Orchestra\Support\Form\Fieldset.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfFieldset()
	{
		$stub = new \Orchestra\Support\Form\Fieldset('foo', function ($f)
		{
			$f->markup(array('class' => 'foo'));
		});

		$this->assertInstanceOf('\Orchestra\Support\Form\Fieldset', $stub);
		$this->assertEquals(array(), $stub->controls);
		$this->assertTrue(isset($stub->name));

		$this->assertEquals(array('class' => 'foo'), $stub->markup);
		$this->assertEquals('foo', $stub->name());

		$stub->markup = array('class' => 'foobar');
		$this->assertEquals(array('class' => 'foobar'), $stub->markup);

	}

	/**
	 * Test Orchestra\Support\Form\Fieldset::of() method.
	 *
	 * @test
	 * @group support
	 */
	public function testOfMethod()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f)
		{
			$f->control('text', 'id', function ($c)
			{
				$c->value('Foobar');
			});
		});		

		$output = $stub->of('id');

		$this->assertEquals('Foobar', $output->value);
		$this->assertEquals('Id', $output->label);
		$this->assertEquals('id', $output->id);
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset::control() method.
	 *
	 * @test
	 * @group support
	 */
	public function testControlMethod()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f)
		{
			$f->control('text', 'text_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('input:email', 'email_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('password', 'password_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('textarea', 'textarea_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('radio', 'radio_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->checked(true);
			});

			$f->control('checkbox', 'checkbox_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->checked(true);
			});

			$f->control('select', 'select_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->options(array(
					'yes' => 'Yes',
					'no'  => 'No',
				));
			});

			$f->control('text', 'a', 'A');

			$f->control('text', function($c)
			{
				$c->name('b')->label('B')->value('value-of-B');
			});
		});

		$output = $stub->of('text_foo');

		$this->assertEquals('text_foo', $output->id);
		$this->assertEquals(\Form::text('text_foo', 'foobar'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('email_foo');

		$this->assertEquals('email_foo', $output->id);
		$this->assertEquals(\Form::email('email_foo', 'foobar'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('password_foo');
		
		$this->assertEquals('password_foo', $output->id);
		$this->assertEquals(\Form::password('password_foo'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('textarea_foo');

		$this->assertEquals('textarea_foo', $output->id);
		$this->assertEquals(\Form::textarea('textarea_foo', 'foobar'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('radio_foo');

		$this->assertEquals('radio_foo', $output->id);
		$this->assertEquals(\Form::radio('radio_foo', 'foobar', true), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('checkbox_foo');

		$this->assertEquals('checkbox_foo', $output->id);
		$this->assertEquals(\Form::checkbox('checkbox_foo', 'foobar', true), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('select_foo');

		$this->assertEquals('select_foo', $output->id);
		$this->assertEquals(\Form::select('select_foo', array('yes' => 'Yes', 'no' => 'No'), 'foobar'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$output = $stub->of('a');

		$this->assertEquals('a', $output->id);
		$this->assertEquals(\Form::text('a'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));

		$controls = $stub->controls;
		$output   = end($controls);

		$this->assertEquals(\Form::text('b', 'value-of-B'), 
			 call_user_func($output->field, new \Laravel\Fluent, $output));
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset::of() method throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testOfMethodThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$output = $stub->of('id');
	}

	/**
	 * Test Orchestra\Support\Form\Grid::markup() method.
	 *
	 * @test
	 * @group support
	 */
	public function testMarkupMethod()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function () {});

		$refl   = new \ReflectionObject($stub);
		$markup = $refl->getProperty('markup');
		$markup->setAccessible(true);

		$stub->markup(array('class' => 'foo'));

		$this->assertEquals(array('class' => 'foo'), $markup->getValue($stub));
		$this->assertEquals(array('class' => 'foo'), $stub->markup());

		$stub->markup('id', 'foobar');

		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $markup->getValue($stub));
		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $stub->markup());
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$stub->invalid_method();
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$invalid = $stub->invalid_property;
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$stub->invalid_property = array('foo');
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$stub->markup = 'foo';
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub = new \Orchestra\Support\Form\Fieldset(function ($f) {});

		$invalid = isset($stub->invalid_property) ? true : false;
	}

	/**
	 * Test Orchestra\Support\Form\Fieldset::render() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testRenderMethodThrowsException()
	{
		\Orchestra\Support\Form\Fieldset::render(
			array(),
			new \Laravel\Fluent(array('method' => 'foo'))
		);
	}
}