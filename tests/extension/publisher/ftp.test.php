<?php

Bundle::start('orchestra');

class ExtensionPublisherFTPTest extends Orchestra\Testable\TestCase {

	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	private $user = null;

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Extension\Publisher\FTP
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->user = Orchestra\Model\User::find(1);
		$this->stub = new Orchestra\Extension\Publisher\FTP;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

		parent::tearDown();
	}

	/**
	 * Get FTP Client Mock
	 *
	 * @access protected
	 * @return Hybrid\FTP
	 */
	protected function getMockFTP()
	{
		$mock = $this->getMock('Hybrid\FTP', array(
			'setup', 
			'connect',
			'chmod',
		));

		$mock->expects($this->any())
			->method('setup')
			->will($this->returnValue(true));

		$mock->expects($this->any())
			->method('connect')
			->will($this->returnValue(true));

		$mock->expects($this->any())
			->method('chmod')
			->will($this->returnValue(true));

		return $mock;
	}

	/**
	 * Test instanceof stub
	 */
	public function testInstanceOfFtp()
	{
		$this->assertInstanceOf('Orchestra\Extension\Publisher\Driver', $this->stub);
		$this->assertFalse($this->stub->connected());
	}

	/**
	 * Test Orchestra\Extension\Publisher\FTP::connect() method.
	 *
	 * @test
	 */
	public function testConnectMethod()
	{
		$mock = $this->getMockFTP();

		$this->stub->attach($mock);
		$this->stub->connect();

		$refl       = new \ReflectionObject($this->stub);
		$connection = $refl->getProperty('connection');
		$connection->setAccessible(true);

		$this->assertEquals($connection->getValue($this->stub), $this->stub->connection());
	}

	/**
	 * Test Orchestra\Extension\Publisher\FTP::connect() method would 
	 * throw an exception.
	 *
	 * @expectedException Hybrid\FTP\ServerException
	 */
	public function testConnectMethodThrowsException()
	{
		$mock = $this->getMock('Hybrid\FTP', array(
			'setup', 
			'connect',
		));
		$mock->expects($this->any())
			->method('setup')
			->will($this->returnValue(true));
		$mock->expects($this->any())
			->method('connect')
			->will($this->throwException(new Hybrid\FTP\ServerException));

		$this->stub->attach($mock);
		$this->stub->connect();
	}
}