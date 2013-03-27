<?php namespace Orchestra\Tests\Routing;

\Bundle::start('orchestra');

class SettingsTest extends \Orchestra\Testable\TestCase {
	
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

		parent::tearDown();
	}

	/**
	 * Test Request GET (orchestra)/settings without auth
	 *
	 * @test
	 * @group routing
	 */
	public function testGetSettingsPageWithoutAuth()
	{
		$this->call('orchestra::settings@index');
		$this->assertRedirectedTo(handles('orchestra::login'));
	}

	/**
	 * Test Request GET (orchestra)/settings
	 *
	 * @test
	 * @group routing
	 */
	public function testGetSettingsPage()
	{
		$this->be($this->user);
		$this->call('orchestra::settings@index');
		$this->assertViewIs('orchestra::settings.index');
	}

	/**
	 * Test Request POST (orchestra)/settings
	 *
	 * @test
	 * @group routing
	 */
	public function testPostSettingsPageIsSuccessful()
	{
		$this->be($this->user);

		$post1 = array(
			'site_name'              => 'Foo',
			'site_description'       => 'Foobar',
			'site_user_registration' => 'no',

			'email_default'          => 'sendmail',
			'email_from'             => 'admin@codenitive.com',
			'email_smtp_host'        => '',
			'email_smtp_port'        => '',
			'email_smtp_username'    => '',
			'email_smtp_password'    => '',
			'email_smtp_encryption'  => '',
			'email_sendmail_command' => '/usr/sbin/sendmail -bs',
			'stmp_change_password'   => 'no',
		);

		$this->call('orchestra::settings@index', array(), 'POST', $post1);
		$this->assertRedirectedTo(handles('orchestra::settings'));
		$this->assertEquals('Foo', memorize('site.name'));
		$this->assertEquals('Foobar', memorize('site.description'));

		$post2 = array(
			'site_name'              => 'Foobar',
			'site_description'       => 'Foo',
			'site_user_registration' => 'no',

			'email_default'          => 'smtp',
			'email_from'             => 'admin@codenitive.com',
			'email_smtp_host'        => 'smtp.codenitive.com',
			'email_smtp_port'        => '25',
			'email_smtp_username'    => 'admin@codenitive.com',
			'email_smtp_password'    => 'whatpassword?',
			'email_smtp_encryption'  => 'ssl',
			'email_sendmail_command' => '',
			'stmp_change_password'   => 'no',
		);

		$this->call('orchestra::settings@index', array(), 'POST', $post2);
		$this->assertRedirectedTo(handles('orchestra::settings'));
		$this->assertEquals('Foobar', memorize('site.name'));
		$this->assertEquals('Foo', memorize('site.description'));
	}

	/**
	 * Test Request POST (orchestra)/settings failed
	 *
	 * @test
	 * @group routing
	 */
	public function testPostSettingsPageFailed()
	{
		$this->be($this->user);
		$this->call('orchestra::settings@index', array(), 'POST', array(
			'site_name' => "Hello"
		));
		$this->assertRedirectedTo(handles('orchestra::settings'));
		$this->assertFalse('Hello' === memorize('site.name'));
	}
}