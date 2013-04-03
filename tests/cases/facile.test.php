<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class FacileTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Facile::$templates = array(
			'default' => \IoC::resolve('\Orchestra\Facile\Template\Base'),
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
		$stub = \Orchestra\Facile::make('default', array('view' => 'home.foo'));

		$refl   = new \ReflectionObject($stub);
		$data   = $refl->getProperty('data');

		$data->setAccessible(true);

		$result   = $data->getValue($stub);
		$expected = array(
			'view'   => 'home.foo',
			'data'   => array(),
			'status' => 200,
		); 

		$this->assertInstanceOf('\Orchestra\Facile\Response', $stub);
		$this->assertEquals($expected, $result);
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
	 * Test Orchestra\Facile::view() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testViewMethod()
	{
		$stub = \Orchestra\Facile::view('home.foo', array('foo' => 'foo is awesome'));

		$refl   = new \ReflectionObject($stub);
		$data   = $refl->getProperty('data');

		$data->setAccessible(true);

		$result   = $data->getValue($stub);
		$expected = array(
			'view'   => 'home.foo',
			'data'   => array('foo' => 'foo is awesome'),
			'status' => 200,
		); 

		$this->assertInstanceOf('\Orchestra\Facile\Response', $stub);
		$this->assertEquals($expected, $result);
	}

	/**
	 * Test Orchestra\Facile::with() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testWithMethod()
	{
		$stub = \Orchestra\Facile::with(array('foo' => 'foo is awesome'));

		$refl   = new \ReflectionObject($stub);
		$data   = $refl->getProperty('data');

		$data->setAccessible(true);

		$result   = $data->getValue($stub);
		$expected = array(
			'view'   => null,
			'data'   => array('foo' => 'foo is awesome'),
			'status' => 200,
		); 

		$this->assertInstanceOf('\Orchestra\Facile\Response', $stub);
		$this->assertEquals($expected, $result);
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
	 * template isn't an instance of Orchestra\Facile\Template\Driver.
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

class ValidFacileTemplateStub extends \Orchestra\Facile\Template\Driver {

	public function compose_html($data)
	{
		return 'foo';
	}
}

class InvalidFacileTemplateStub {}
