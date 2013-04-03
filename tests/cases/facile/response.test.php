<?php namespace Orchestra\Tests\Facile;

\Bundle::start('orchestra');

class ReponseTest extends \PHPUnit_Framework_TestCase {

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
	 * Test construct an instance of Orchestra\Facile\Response.
	 *
	 * @test
	 * @group facile
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(),
			'json'
		);

		$refl   = new \ReflectionObject($stub);
		$data   = $refl->getProperty('data');
		$format = $refl->getProperty('format');

		$data->setAccessible(true);
		$format->setAccessible(true);

		$this->assertEquals(array('view' => null, 'data' => array(), 'status' => 200),
			$data->getValue($stub));
		$this->assertEquals('json', $format->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::view() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testViewMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(),
			'json'
		);

		$stub->view('foo.bar');

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals('foo.bar', $result['view']);
	}

	/**
	 * Test Orchestra\Facile\Response::with() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testWithMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(),
			'json'
		);

		$stub->with('foo', 'bar');
		$stub->with(array('foobar' => 'foo'));

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals(array('foo' => 'bar', 'foobar' => 'foo'), $result['data']);
	}

	/**
	 * Test Orchestra\Facile\Response::status() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testStatusMethod()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(),
			'json'
		);

		$stub->status(500);

		$refl = new \ReflectionObject($stub);
		$data = $refl->getProperty('data');
		$data->setAccessible(true);

		$result = $data->getValue($stub);

		$this->assertEquals(500, $result['status']);
	}

	/**
	 * Test Orchestra\Facile\Response::template() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testTemplateMethod()
	{
		\Orchestra\Facile::template('foo', function ()
		{
			return new ValidFacileTemplateStub;
		});

		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(),
			'json'
		);

		$stub->template('foo');

		$refl     = new \ReflectionObject($stub);
		$template = $refl->getProperty('template');
		$template->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Tests\Facile\ValidFacileTemplateStub', 
			$template->getValue($stub));

		$stub->template(new \Orchestra\Facile\Template\Base);

		$refl     = new \ReflectionObject($stub);
		$template = $refl->getProperty('template');
		$template->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Facile\Template\Base', $template->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::format() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testFormatMethod()
	{
		$mock = $this->getMockBuilder('\Orchestra\Facile\Template\Base')
				->disableOriginalConstructor()
					->setMethods(array('format'))
					->getMock();

		$mock->expects($this->once())
			->method('format')
			->will($this->returnValue('jsonp'));

		$stub = new \Orchestra\Facile\Response(
			$mock,
			array(),
			null
		);

		$this->assertEquals('jsonp', $stub->format()->format);

		$stub->format('md');

		$refl   = new \ReflectionObject($stub);
		$format = $refl->getProperty('format');
		$format->setAccessible(true);

		$this->assertEquals('md', $format->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Response::__get() method with invalid arguments.
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetMethodWithInvalidArgument()
	{
		$stub = new \Orchestra\Facile\Response(
			new \Orchestra\Facile\Template\Base,
			array(
				'view' => 'foo.bar',
				'data' => array('foo' => 'foo is awesome'),
				'status' => 404,
			),
			'json'
		);

		$data = $stub->data;
	}

	/**
	 * Test Orchestra\Facile\Response::__toString() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testToStringMethod()
	{
		$mock1 = $this->getMockBuilder('\Orchestra\Facile\Template\Base')
				->disableOriginalConstructor()
					->setMethods(array('compose'))
					->getMock();

		$mock1->expects($this->once())
			->method('compose')
			->will($this->returnValue(json_encode(array('foo' => 'foo is awesome'))));

		$stub1 = new \Orchestra\Facile\Response(
			$mock1,
			array(),
			'json'
		);

		ob_start();
		echo $stub1;
		$output1 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('{"foo":"foo is awesome"}', $output1);

		$renderMock = $this->getMockBuilder('RenderableInterface')
				->disableOriginalConstructor()
					->setMethods(array('render'))
					->getMock();

		$renderMock->expects($this->once())
			->method('render')
			->will($this->returnValue('foo is awesome'));

		$mock2 = $this->getMockBuilder('\Orchestra\Facile\Template\Driver')
				->disableOriginalConstructor()
					->setMethods(array('compose'))
					->getMock();
		$mock2->expects($this->once())
			->method('compose')
			->will($this->returnValue($renderMock));

		$stub2 = new \Orchestra\Facile\Response(
			$mock2,
			array(),
			'json'
		);

		ob_start();
		echo $stub2;
		$output2 = ob_get_contents();
		ob_end_clean();

		$this->assertEquals('foo is awesome', $output2);
	}
}

class ValidFacileTemplateStub extends \Orchestra\Facile\Template\Base {

	public function compose_html($view = null, $data = array(), $status = 200)
	{
		return 'foo is awesome';
	}
}
