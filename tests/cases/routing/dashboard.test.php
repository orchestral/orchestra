<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class DashboardTest extends \Orchestra\Testable\TestCase {

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

		$this->user = \Orchestra\Model\User::find(1);
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
	 * Test Request GET (orchestra) without auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetDashboardPageWithoutAuth()
	{
		$this->call('orchestra::dashboard@index');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)
	 *
	 * @test
	 * @group routing
	 */
	public function testGetDashboardPage()
	{
		$this->be($this->user);
		$this->call('orchestra::dashboard@index');
		$this->assertViewIs('orchestra::dashboard.index');
	}

	/**
	 * Test Language Locale is set properly.
	 *
	 * @test
	 * @group routing
	 */
	public function testInitiateCoreCheckLanguageLocale()
	{
		\Orchestra\Core::shutdown();

		\URI::$uri = 'ru/home';
		\Config::set('application.language', 'en');
		\Config::set('application.languages', array('en', 'ru'));

		\Orchestra\Core::start();

		$this->assertEquals('ru', \Config::get('application.language'));
	}	

	/**
	 * Test Request Corrupted Installation.
	 *
	 * @test
	 * @group routing
	 */
	public function testInitiateCoreStartIsCorruptedInstallation()
	{
		$this->assertTrue(\Orchestra\Installer::$status);

		$memory = \Orchestra\Core::memory();
		$memory->put('site', array());

		\Orchestra\Core::shutdown();
		\Orchestra\Core::start();

		$this->assertFalse(\Orchestra\Installer::$status);
	}
}