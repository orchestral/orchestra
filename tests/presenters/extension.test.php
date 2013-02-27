<?php namespace Orchestra\Tests\Presenters;

\Bundle::start('orchestra');

class ExtensionTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setting instance.
	 *
	 * @var Laravel\Fluent
	 */
	protected $rows = null;

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

		// Orchestra settings are stored using Orchestra\Memory, we need to
		// fetch it and convert it to Fluent (to mimick Eloquent properties).
		$memory     = \Orchestra\Core::memory();
		$this->rows = new \Laravel\Fluent(array(
			'handles' => 'foohandler'
		));

		$this->user = \Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->rows);
		unset($this->user);

		parent::tearDown();
	}

	/**
	 * Test Orchestra\Presenter\Extension::form().
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfExtensionForm()
	{
		$this->be($this->user);

		$stub = \Orchestra\Presenter\Extension::form('foo', $this->rows);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Form', $stub);
		$this->assertEquals(\Orchestra\Form::of('orchestra.extension: foo'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid);

		ob_start();
		echo $stub->render();
		$content = ob_get_contents();
		ob_end_clean();

		$this->assertContains('foohandler', $content);
		$this->assertContains(handles("orchestra::extensions/update/foo"), $content);
	}
}