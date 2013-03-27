<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class PublisherTest extends \Orchestra\Testable\TestCase {
	
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

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('public', $base_path.'public'.DS);

		\Orchestra\Extension::detect(array(
			DEFAULT_BUNDLE => "{$base_path}application".DS,
		));

		$this->user = \Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\File::rmdir(path('public').'bundles'.DS.DEFAULT_BUNDLE.DS);

		set_path('public', path('base').'public'.DS);
		unset($this->user);

		parent::tearDown();
	}

	/**
	 * Get FTP Client Mock
	 *
	 * @access protected
	 * @return Orchestra\Extenstion\Publisher\FTP
	 */
	protected function getMockPublisherFTP()
	{
		$mock = $this->getMock('\Orchestra\Extension\Publisher\FTP', array(
			'connect',
			'connected',
			'execute',
			'upload',
		));

		$mock->expects($this->any())
			->method('connect')
			->will($this->returnValue(true));
		$mock->expects($this->any())
			->method('connected')
			->will($this->returnValue(true));
		$mock->expects($this->any())
			->method('execute')
			->will($this->returnValue(true));
		$mock->expects($this->any())
			->method('upload')
			->will($this->returnValue(true));

		return $mock;
	}

	/**
	 * Test Request GET (orchestra)/publisher without auth.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetPublisherIndexPageWithoutAuth()
	{
		$this->call('orchestra::publisher@index');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}
	
	/**
	 * Test Request GET (orchestra)/publisher
	 *
	 * @test
	 * @group routing
	 */
	public function testGetPublisherIndexPage()
	{
		$mock = $this->getMockPublisherFTP();

		\Orchestra\Extension\Publisher::$registrar = array();
		\Orchestra\Extension\Publisher::$drivers   = array();
		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'sftp');
		\Orchestra\Extension\Publisher::extend('sftp', function () use ($mock)
		{
			return $mock;
		});

		$this->be($this->user);
		$this->call('orchestra::publisher@index');
		$this->assertRedirectedTo(handles('orchestra::publisher/ftp'));
	}

	/**
	 * Test Request GET (orchestra)/publisher/ftp without auth.
	 *
	 * @test
	 * @group routing
	 */
	public function testGetPublisherFtpPageWithoutAuth()
	{
		$this->be(null);
		$this->call('orchestra::publisher@ftp');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/publisher/ftp
	 *
	 * @test
	 * @group routing
	 */
	public function testGetPublisherFtpPage()
	{
		$this->be($this->user);
		$this->call('orchestra::publisher@ftp');
		$this->assertViewIs('orchestra::publisher.ftp');
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp without auth.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostPublisherFtpPageWithoutAuth()
	{
		$this->be(null);
		$this->call('orchestra::publisher@ftp');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp
	 *
	 * @test
	 * @group routing
	 */
	public function testPostPublisherFtpPage()
	{
		$mock = $this->getMockPublisherFTP();

		\Orchestra\Extension\Publisher::queue(DEFAULT_BUNDLE);
		\Orchestra\Extension\Publisher::$registrar = array();
		\Orchestra\Extension\Publisher::$drivers   = array();
		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'sftp');
		\Orchestra\Extension\Publisher::extend('sftp', function () use ($mock)
		{
			return $mock;
		});

		$this->be($this->user);
		$this->call('orchestra::publisher@ftp', array(), 'POST', array());
		$this->assertRedirectedTo(handles('orchestra::publisher/ftp'));
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp with invalid FTP 
	 * credential.
	 *
	 * @test
	 * @group routing
	 */
	public function testPostPublisherFtpPageInvalidCredential()
	{	
		$mock = $this->getMock('\Orchestra\Extension\Publisher\FTP', array(
			'connect',
		));

		$mock->expects($this->any())
			->method('connect')
			->will($this->throwException(new \Orchestra\Support\FTP\ServerException));

		\Orchestra\Extension\Publisher::$registrar = array();
		\Orchestra\Extension\Publisher::$drivers   = array();
		\Orchestra\Core::memory()->put('orchestra.publisher.driver', 'iftp');
		\Orchestra\Extension\Publisher::extend('iftp', function () use ($mock)
		{
			return $mock;
		});

		$this->be($this->user);
		$this->call('orchestra::publisher@ftp', array(), 'POST', array());
		$this->assertRedirectedTo(handles('orchestra::publisher/ftp'));
		$this->assertNull(\Session::get('orchestra.ftp'));
	}
}