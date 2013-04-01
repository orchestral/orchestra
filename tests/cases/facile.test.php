<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class FacileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		set_path('app', \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'application'.DS);
		set_path('public', \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS);

		\Orchestra\Facile::$templates = array(
			'default' => \IoC::resolve('\Orchestra\Facile\Template'),
		);

		\Orchestra\Facile::template('foo', function ()
		{
			return new ValidFacileTemplateStub;
		});
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		set_path('app', path('base').'application'.DS);
		set_path('public', path('base').'public'.DS);

		\Orchestra\Facile::$templates = array();
	}

	/**
	 * Test Orchestra\Facile::make() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testMakeMethod()
	{
		$stub1 = \Orchestra\Facile::make('default', array('view' => 'home.foo'));
		$this->assertInstanceOf('\Orchestra\Facile', $stub1);

		ob_start();
		echo $stub1;
		$output1 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo', $output1);


		$stub2 = \Orchestra\Facile::make('foo', array('view' => 'home.foo'));
		$this->assertInstanceOf('\Orchestra\Facile', $stub2);

		ob_start();
		echo $stub2;
		$output2 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo', $output2);
	}

	/**
	 * Test Orchestra\Facile::render() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testRenderMethod()
	{
		$stub = \Orchestra\Facile::make('default')
			->view('error.404')
			->with(array('foo' => 'foo is awesome'))
			->status(404)
			->format('json')
			->render();

		$this->assertInstanceOf('\Response', $stub);
		$this->assertEquals(404, $stub->foundation->getStatusCode());
		$this->assertEquals('{"foo":"foo is awesome"}', $stub->content);
	}

	/**
	 * Test Orchestra\Facile::__get() method with invalid arguments.
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodWithInvalidArgument()
	{
		$stub = \Orchestra\Facile::make('default')
			->view('error.404')
			->with(array('foo' => 'foo is awesome'))
			->status(404)
			->format('json');

		$data = $stub->data;
	}

	/**
	 * Test Orchestra\Facile::make() throws exception when using an invalid 
	 * template.
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testMakeMethodThrowsExceptionUsingInvalidTemplate()
	{
		\Orchestra\Facile::make('foobar', array('view' => 'error.404'), 'html');
	}

	/**
	 * Test Orchestra\Facile::template() method
	 *
	 * @test
	 * @group facile
	 */
	public function testTemplateMethod()
	{
		$this->assertInstanceOf('\Orchestra\Tests\ValidFacileTemplateStub', 
			\Orchestra\Facile::$templates['foo']);
	}

	/**
	 * Test Orchestra\Facile::template() method throws exception when 
	 * template isn't an instance of Orchestra\Facile\Driver.
	 *
	 * @group facile
	 * @expectedException \RuntimeException
	 */
	public function testTemplateMethodThrowsException()
	{
		\Orchestra\Facile::template('foobar', function ()
		{
			return new InvalidFacileTemplateStub;
		});
	}
}

class ValidFacileTemplateStub extends \Orchestra\Facile\Driver {

	public function compose_html($data)
	{
		return 'foo';
	}
}

class InvalidFacileTemplateStub {}
