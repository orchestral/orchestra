<?php namespace Orchestra\Tests\Supports;

class SiteTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Support\Site::$items = array();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Support\Site::$items = array();
	}

	/**
	 * Test Orchestra\Support\Site::get() method.
	 *
	 * @test
	 * @group support
	 */
	public function testGetMethod()
	{
		\Orchestra\Support\Site::$items = array(
			'title'       => 'Hello World',
			'description' => 'Just another Hello World'
		);

		$this->assertEquals('Hello World', \Orchestra\Support\Site::get('title'));
		$this->assertNull(\Orchestra\Support\Site::get('title.foo'));
	}

	/**
	 * Test Orchestra\Support\Site::set() method.
	 *
	 * @test
	 * @group support
	 */
	public function testSetMethod()
	{
		\Orchestra\Support\Site::$items = array();

		\Orchestra\Support\Site::set('title', 'Foo');
		\Orchestra\Support\Site::set('foo.bar', 'Foobar');

		$this->assertEquals(array(
			'title' => 'Foo',
			'foo'   => array(
				'bar' => 'Foobar',
			),
		), \Orchestra\Support\Site::$items);
	}

	/**
	 * Test Orchestra\Support\Site::has() method.
	 *
	 * @test
	 * @group support
	 */
	public function testHasMethod()
	{
		\Orchestra\Support\Site::$items = array(
			'title'       => 'Hello World',
			'description' => 'Just another Hello World'
		);

		$this->assertTrue(\Orchestra\Support\Site::has('title'));
		$this->assertFalse(\Orchestra\Support\Site::has('title.foo'));
	}
}