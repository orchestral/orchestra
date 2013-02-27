<?php namespace Orchestra\Tests\Extension;

\Bundle::start('orchestra');

class PublisherTest extends \Orchestra\Testable\TestCase {

	/**
	 * Stub instance.
	 *
	 * @var PublisherStub
	 */
	private $stub = null;

	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	private $user = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'stub');

		\Orchestra\Extension\Publisher::$registrar = array();
		\Orchestra\Extension\Publisher::$drivers   = array();
		
		\Orchestra\Extension\Publisher::extend('stub', function ()
		{
			return new PublisherStub;
		});
		
		\Orchestra\Extension\Publisher::extend('exceptional-stub', function ()
		{
			return new ExceptionalPublisherStub;
		});

		$this->stub = new PublisherStub;
		$this->user = \Orchestra\Model\User::find(1);

		$this->be($this->user);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'ftp');
		\Orchestra\Extension\Publisher::$drivers = array();

		unset($this->stub);
		unset($this->user);

		$this->be(null);

		parent::tearDown();
	}

	/**
	 * Test instance of Orchestra\Extension\Publisher default driver
	 *
	 * @test
	 * @group extension
	 */
	public function testInstanceOfDefaultDriver()
	{
		$this->assertInstanceOf('\Orchestra\Extension\Publisher\Driver',	
			\Orchestra\Extension\Publisher::driver());
		$this->assertInstanceOf('\Orchestra\Tests\Extension\PublisherStub',	
			\Orchestra\Extension\Publisher::driver());
		$this->assertInstanceOf('\Orchestra\Extension\Publisher\FTP',	
			\Orchestra\Extension\Publisher::driver('ftp'));
	}

	/**
	 * Test instance of Orchestra\Extension\Publisher using stub.
	 *
	 * @test
	 * @group extension
	 */
	public function testInstanceOfPublisher()
	{
		$this->assertInstanceOf('\Orchestra\Extension\Publisher\Driver',	
			$this->stub);
		$this->assertInstanceOf('\Orchestra\Extension\Publisher\Driver', 
			\Orchestra\Extension\Publisher::driver('stub'));

		$this->assertTrue($this->stub->upload(DEFAULT_BUNDLE));
		$this->assertTrue($this->stub->connected());
	}

	/**
	 * Test Orchestra\Extension\Publisher::queue() method.
	 *
	 * @test
	 * @group extension
	 */
	public function testQueueMethod()
	{
		\Session::put('orchestra.publisher.queue', array());
		$expected = array('foo');

		\Orchestra\Extension\Publisher::queue($expected);

		$this->assertEquals($expected, \Session::get('orchestra.publisher.queue'));
		$this->assertEquals($expected, \Orchestra\Extension\Publisher::queued());
	}

	/**
	 * Test Orchestra\Extension\Publisher::execute() method.
	 *
	 * @test
	 * @group extension
	 */
	public function testExecuteMethodSuccessful()
	{
		\Session::put('orchestra.publisher.queue', array('foo'));

		$result = \Orchestra\Extension\Publisher::execute(
			new \Orchestra\Messages
		);
		$queue  = \Session::get('orchestra.publisher.queue');

		$this->assertEmpty($queue);
		$this->assertTrue(is_array($queue));
		$this->assertInstanceOf('\Orchestra\Messages', $result);
	}

	/**
	 * Test Orchestra\Extension\Publisher::execute() method fail.
	 *
	 * @test
	 * @group extension
	 */
	public function testExecuteMethodFail()
	{
		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'exceptional-stub');
		\Orchestra\Extension\Publisher::$drivers = array();
		
		\Session::put('orchestra.publisher.queue', array('foo'));

		$result = \Orchestra\Extension\Publisher::execute(
			new \Orchestra\Messages
		);

		$this->assertEquals(array('foo'), \Session::get('orchestra.publisher.queue'));
		$this->assertInstanceOf('\Orchestra\Messages', $result);
		$this->assertEquals(array('Invalid request'), $result->get('error'));
	}

	/**
	 * Test Orchestra\Extension\Publisher::execute() method throws an 
	 * exception when not injecting Orchestra\Messages.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group extension
	 */
	public function testExecuteMethodThrowsInvalidArgumentException()
	{
		\Orchestra\Extension\Publisher::execute(new \Laravel\Fluent);
	}
}

class PublisherStub extends \Orchestra\Extension\Publisher\Driver {
	/**
	 * Get service connection instance.
	 *
	 * @access public
	 * @return Object
	 */
	public function connection()
	{
		return $this;
	}

	/**
	 * Connect to the service.
	 *
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public function connect($config = array())
	{
		// connect to foo.... connected.
	}

	/**
	 * Upload the file.
	 *
	 * @access public
	 * @param  string   $name   Extension name
	 * @return bool
	 */
	public function upload($name)
	{
		return true;
	}

	/**
	 * Verify that the driver is connected to a service.
	 *
	 * @access public
	 * @return bool
	 */
	public function connected()
	{
		return true;
	}
}

class ExceptionalPublisherStub extends \Orchestra\Extension\Publisher\Driver {
	/**
	 * Get service connection instance.
	 *
	 * @access public
	 * @return Object
	 */
	public function connection()
	{
		return $this;
	}

	/**
	 * Connect to the service.
	 *
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public function connect($config = array())
	{
		// connect to foo.... connected.
	}

	/**
	 * Upload the file.
	 *
	 * @access public
	 * @param  string   $name   Extension name
	 * @return bool
	 */
	public function upload($name)
	{
		throw new \Exception('Invalid request');
	}

	/**
	 * Verify that the driver is connected to a service.
	 *
	 * @access public
	 * @return bool
	 */
	public function connected()
	{
		return true;
	}
}