<?php namespace Orchestra\Tests\Presenters;

\Bundle::start('orchestra');

class SettingTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setting instance.
	 *
	 * @var Laravel\Fluent
	 */
	protected $rows = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		// Orchestra settings are stored using Orchestra\Memory, we need to
		// fetch it and convert it to Fluent (to mimick Eloquent properties).
		$memory     = \Orchestra\Core::memory();
		$this->rows = new \Laravel\Fluent(array(
			'site_name'              => $memory->get('site.name', ''),
			'site_description'       => $memory->get('site.description', ''),
			'site_user_registration' => ($memory->get('site.users.registration', false) ? 'yes' : 'no'),

			'email_default'          => $memory->get('email.default', ''),
			'email_from'             => $memory->get('email.from', ''),
			'email_smtp_host'        => $memory->get('email.transports.smtp.host', ''),
			'email_smtp_port'        => $memory->get('email.transports.smtp.port', ''),
			'email_smtp_username'    => $memory->get('email.transports.smtp.username', ''),
			'email_smtp_password'    => $memory->get('email.transports.smtp.password', ''),
			'email_smtp_encryption'  => $memory->get('email.transports.smtp.encryption', ''),
			'email_sendmail_command' => $memory->get('email.transports.sendmail.command', ''),
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->rows);

		parent::tearDown();
	}

	/**
	 * Test Orchestra\Presenter\Setting::form().
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfSettingForm()
	{
		$stub = \Orchestra\Presenter\Setting::form($this->rows);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Form', $stub);
		$this->assertEquals(\Orchestra\Form::of('orchestra.settings'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid);
	}
}