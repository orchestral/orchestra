<?php namespace Orchestra\Tests\Supports\Helpers;

class FacadeTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra\Support\Helpers\Facade::__callStatic() method.
	 *
	 * @test
	 */
	public function testCallStaticMethod()
	{
		$sample = array('foo');
		$this->assertEquals(array('foo', 'foobar'), 
			ArrayStub::push($sample, 'foobar'));

		$this->assertEquals(array('foo'), 
			ArrayStub::pop($sample));
	}

	/**
	 * Test Orchestra\Support\Helpers\Facade::__callStatic() method throws 
	 * an exception when method is not callable.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testCallStaticMethodThrowsException()
	{
		ArrayStub::foo($sample, 'foobar'));
	}
}

class ArrayStub extends \Orchestra\Support\Helpers\Facade {
	protected static $prefix = 'array_';
}