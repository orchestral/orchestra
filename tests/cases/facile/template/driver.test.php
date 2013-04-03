<?php namespace Orchestra\Tests\Facile\Template;

\Bundle::start('orchestra');

class DriverTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Facile\Template\Driver::format() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testFormatMethod()
	{
		\Request::foundation()->request->add(array('format' => 'xml'));

		$stub = new TemplateStub;
		$this->assertEquals('xml', $stub->format());
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method.
	 *
	 * @test 
	 * @group facile
	 */
	public function testComposeMethod()
	{
		$stub = new TemplateStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$this->assertEquals('foo', $stub->compose('foo', $data));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method return response with 
	 * error 406 when given an invalid format.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeMethodReturnResponseError406WhenGivenInvalidFormat()
	{
		$stub = new TemplateStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$response = $stub->compose('foobar', $data);
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(406, $response->foundation->getStatusCode());
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::compose() method throws exception 
	 * when given method isn't available.
	 *
	 * @group facile
	 * @expectedException \RuntimeException
	 */
	public function testComposeMethodThrowsExceptionWhenMethodNotAvailable()
	{
		$stub = new TemplateStub;
		$data = array(
			'view'   => null,
			'data'   => array(),
			'status' => 200,
		);

		$stub->compose('json', $data);
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item has 
	 * to_array().
	 *
	 * @test 
	 * @group facile
	 */
	public function testTransformMethodWhenItemHasToArray()
	{
		$mock = $this->getMockBuilder('\ArrayCollection')
					->disableOriginalConstructor()
					->setMethods(array('to_array'))
					->getMock();

		$mock->expects($this->once())
			->method('to_array')
			->will($this->returnValue('foobar'));

		$stub = new TemplateStub;
		$this->assertEquals('foobar', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is 
	 * instance of Laravel\Database\Eloquent\Model.
	 *
	 * @test 
	 * @group facile
	 */
	public function testTransformMethodWhenItemIsInstanceOfEloquent()
	{
		$mock = $this->getMockBuilder('\Laravel\Database\Eloquent\Model')
					->disableOriginalConstructor()
					->setMethods(array('to_array'))
					->getMock();

		$mock->expects($this->once())
			->method('to_array')
			->will($this->returnValue('foobar'));

		$stub = new TemplateStub;
		$this->assertEquals('foobar', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is an 
	 * array.
	 *
	 * @test 
	 * @group facile
	 */
	public function testTransformMethodWhenItemIsArray()
	{
		$mock = $this->getMockBuilder('\ArrayCollection')
					->disableOriginalConstructor()
					->setMethods(array('to_array'))
					->getMock();

		$mock->expects($this->once())
			->method('to_array')
			->will($this->returnValue('foobar'));

		$stub = new TemplateStub;
		$this->assertEquals(array('foobar'), $stub->transform(array($mock)));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item has 
	 * render().
	 *
	 * @test 
	 * @group facile
	 */
	public function testTransformMethodWhenItemHasRender()
	{
		$mock = $this->getMockBuilder('\Orchestra\Support\Table')
					->disableOriginalConstructor()
					->setMethods(array('render'))
					->getMock();

		$mock->expects($this->once())
			->method('render')
			->will($this->returnValue('foobar'));

		$stub = new TemplateStub;
		$this->assertEquals('foobar', $stub->transform($mock));
	}

	/**
	 * Test Orchestra\Facile\Template\Driver::transform() method when item is instance 
	 * of Laravel\Paginator
	 *
	 * @test 
	 * @group facile
	 */
	public function testTransformMethodWhenItemInstanceOfPaginator()
	{
		$mock = $this->getMockBuilder('\Laravel\Database\Eloquent\Model')
					->disableOriginalConstructor()
					->setMethods(array('to_array'))
					->getMock();

		$mock->expects($this->once())
			->method('to_array')
			->will($this->returnValue('foobar'));

		$paginated = \Laravel\Paginator::make(array('foo' => $mock), 1, 10);

		$stub = new TemplateStub;
		$this->assertEquals(array('results' => array('foo' => 'foobar'), 'links' => ''), 
			$stub->transform($paginated));
	}
}

class TemplateStub extends \Orchestra\Facile\Template\Driver {
	
	protected $formats = array('html', 'json', 'foo');

	public function compose_foo($data)
	{
		return 'foo';
	}
}