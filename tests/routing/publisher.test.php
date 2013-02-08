<?php

Bundle::start('orchestra');

class RoutingPublisherTest extends Orchestra\Testable\TestCase {
	
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

		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('public', $base_path.'public'.DS);

		Orchestra\Extension::detect(array(
			DEFAULT_BUNDLE => "{$base_path}application".DS,
		));

		$this->user = Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		File::rmdir(path('public').'bundles'.DS.DEFAULT_BUNDLE.DS);

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
		$mock = $this->getMock('Orchestra\Extension\Publisher\FTP', array(
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
	 */
	public function testGetPublisherIndexPageWithoutAuth()
	{
		$response = $this->call('orchestra::publisher@index');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}
	
	/**
	 * Test Request GET (orchestra)/publisher
	 *
	 * @test
	 */
	public function testGetPublisherIndexPage()
	{
		$this->be($this->user);

		$mock = $this->getMockPublisherFTP();

		Orchestra\Extension\Publisher::$registrar = array();
		Orchestra\Extension\Publisher::$drivers   = array();

		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'sftp');

		Orchestra\Extension\Publisher::extend('sftp', function () use ($mock)
		{
			return $mock;
		});

		$response = $this->call('orchestra::publisher@index');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::publisher/ftp'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/publisher/ftp without auth.
	 *
	 * @test
	 */
	public function testGetPublisherFtpPageWithoutAuth()
	{
		$this->be(null);

		$response = $this->call('orchestra::publisher@ftp');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request GET (orchestra)/publisher/ftp
	 *
	 * @test
	 */
	public function testGetPublisherFtpPage()
	{
		$this->be($this->user);

		$response = $this->call('orchestra::publisher@ftp');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::publisher.ftp', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp without auth.
	 *
	 * @test
	 */
	public function testPostPublisherFtpPageWithoutAuth()
	{
		$this->be(null);

		$response = $this->call('orchestra::publisher@ftp');

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp
	 *
	 * @test
	 */
	public function testPostPublisherFtpPage()
	{
		$this->be($this->user);
		$mock = $this->getMockPublisherFTP();

		Orchestra\Extension\Publisher::queue(DEFAULT_BUNDLE);

		Orchestra\Extension\Publisher::$registrar = array();
		Orchestra\Extension\Publisher::$drivers   = array();

		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'sftp');

		Orchestra\Extension\Publisher::extend('sftp', function () use ($mock)
		{
			return $mock;
		});

		$response = $this->call('orchestra::publisher@ftp', array(), 'POST', array());

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::publisher/ftp'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Request POST (orchestra)/publisher/ftp with invalid FTP 
	 * credential.
	 *
	 * @test
	 */
	public function testPostPublisherFtpPageInvalidCredential()
	{
		$this->be($this->user);
		
		$mock = $this->getMock('Orchestra\Extension\Publisher\FTP', array(
			'connect',
		));
		$mock->expects($this->any())
			->method('connect')
			->will($this->throwException(new Hybrid\FTP\ServerException));

		Orchestra\Extension\Publisher::$registrar = array();
		Orchestra\Extension\Publisher::$drivers   = array();

		Orchestra\Core::memory()->put('orchestra.publisher.driver', 'iftp');

		Orchestra\Extension\Publisher::extend('iftp', function () use ($mock)
		{
			return $mock;
		});

		$response = $this->call('orchestra::publisher@ftp', array(), 'POST', array());

		$this->assertInstanceOf('Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('orchestra::publisher/ftp'), 
			$response->foundation->headers->get('location'));
		$this->assertNull(Session::get('orchestra.ftp'));
	}
}