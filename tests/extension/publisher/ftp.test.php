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

		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('public', $base_path.'public'.DS);

		Orchestra\Extension::detect(array(
			DEFAULT_BUNDLE => "{$base_path}application".DS,
		));

		// Default bundle is added so we can do recursive chmod instead 
		// just normal chmod.
		File::mkdir(path('public').'bundles'.DS.DEFAULT_BUNDLE.DS);

		$this->user = Orchestra\Model\User::find(1);
		$this->stub = new Orchestra\Extension\Publisher\FTP;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		File::rmdir(path('public').'bundles'.DS.DEFAULT_BUNDLE.DS);

		set_path('public', path('base').'public'.DS);
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
			'ls',
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

		$mock->expects($this->any())
			->method('ls')
			->will($this->returnValue(array('foo')));

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

	/**
	 * Test Orchestra\Extension\Publisher\FTP::upload() method.
	 *
	 * @test
	 */
	public function testUploadMethod()
	{
		$mock = $this->getMockFTP();

		$this->stub->attach($mock);
		$this->stub->connect();

		$this->assertTrue($this->stub->upload(DEFAULT_BUNDLE));
		$this->assertEquals('/codenitive.com/public_html', 
			$this->stub->base_path('/home/crynobone/codenitive.com/public_html'));
	}
}