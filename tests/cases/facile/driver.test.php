<?php namespace Orchestra\Tests\Facile;

class DriverTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Facile\Driver::format() method.
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
	 * Test Orchestra\Facile\Driver::compose() method.
	 *
	 * @test 
	 * @group facile
	 */
	public function testComposeMethod()
	{
		$stub = new TemplateStub;
		$this->assertEquals('foo', $stub->compose('foo', array()));
	}

	/**
	 * Test Orchestra\Facile\Driver::compose() method throws exception 
	 * when given an invalid format.
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testComposeMethodThrowsExceptionWhenGivenInvalidFormat()
	{
		$stub = new TemplateStub;
		$stub->compose('foobar', array());
	}

	/**
	 * Test Orchestra\Facile\Driver::compose() method throws exception 
	 * when given method isn't available.
	 *
	 * @group facile
	 * @expectedException \RuntimeException
	 */
	public function testComposeMethodThrowsExceptionWhenMethodNotAvailable()
	{
		$stub = new TemplateStub;
		$stub->compose('json', array());
	}
}

class TemplateStub extends \Orchestra\Facile\Driver {
	
	protected $format = array('html', 'json', 'foo');

	public function compose_foo($data)
	{
		return 'foo';
	}
}