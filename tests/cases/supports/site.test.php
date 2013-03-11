<?php namespace Orchestra\Tests\Supports;

class SiteTest extends \Orchestra\Testable\TestCase {
	
	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	protected $user = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		\Orchestra\Support\Site::$items = array();

		$this->user = \Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Support\Site::$items = array();
		unset($this->user);
		
		parent::tearDown();
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
			'description' => 'Just another Hello World',
			'hello'       => null,
		);

		$this->assertTrue(\Orchestra\Support\Site::has('title'));
		$this->assertFalse(\Orchestra\Support\Site::has('title.foo'));
		$this->assertFalse(\Orchestra\Support\Site::has('hello'));
	}

	/**
	 * Test Orchestra\Support\Site::forget() method.
	 *
	 * @test
	 * @group support
	 */
	public function testForgetMethod()
	{
		\Orchestra\Support\Site::$items = array(
			'title'       => 'Hello World',
			'description' => 'Just another Hello World',
			'hello'       => null,
			'foo'         => array(
				'hello' => 'foo',
				'bar'   => 'foobar',
			),
		);

		\Orchestra\Support\Site::forget('title');
		\Orchestra\Support\Site::forget('hello');
		\Orchestra\Support\Site::forget('foo.bar');

		$this->assertFalse(\Orchestra\Support\Site::has('title'));
		$this->assertTrue(\Orchestra\Support\Site::has('description'));
		$this->assertFalse(\Orchestra\Support\Site::has('hello'));
		$this->assertEquals(array('hello' => 'foo'), \Orchestra\Support\Site::get('foo'));
	}

	/**
	 * Test localtime() return proper datetime.
	 *
	 * @test
	 * @group support
	 */
	public function testLocalTimeReturnProperDateTime()
	{
		\Config::set('application.timezone', 'UTC');
		$meta = \Orchestra\Support\Memory::make('user');

		$this->assertEquals(new \DateTimeZone('UTC'),
				\Orchestra\Support\Site::localtime('2012-01-01 00:00:00')->getTimezone());
		
		$meta->put("timezone.1", 'Asia/Kuala_Lumpur');
		$this->be($this->user);

		$this->assertEquals(new \DateTimeZone('Asia/Kuala_Lumpur'),
				\Orchestra\Support\Site::localtime('2012-01-01 00:00:00')->getTimezone());
	}
}