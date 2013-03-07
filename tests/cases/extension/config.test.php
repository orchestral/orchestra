<?php namespace Orchestra\Tests\Extension;

\Bundle::start('orchestra');

class ConfigTest extends \Orchestra\Testable\TestCase {
	
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
		$this->be($this->user);

		$base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('app', $base_path.'application'.DS);
		set_path('orchestra.extension', $base_path.'bundles'.DS);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);
		$this->be(null);

		parent::tearDown();

		set_path('app', path('base').'application'.DS);
		set_path('orchestra.extension', path('bundle'));
	}

	/**
	 * Test map DEFAULT_BUNDLE configuration.
	 *
	 * @test
	 * @group extension
	 */
	public function testMapMethod()
	{
		\Config::set('application::foo.bar', 'foobar');
		$this->restartApplication();

		\Orchestra\Extension::detect();
		\Orchestra\Extension::activate(DEFAULT_BUNDLE);

		\Orchestra\Extension\Config::map(DEFAULT_BUNDLE, array(
			'foo' => 'application::foo',
		));

		$memory = \Orchestra\Core::memory();

		$this->assertEquals(\Config::get('application::foo'), 
			$memory->get('extension_application.foo'));

		\Orchestra\Extension::deactivate(DEFAULT_BUNDLE);
	}
}