<?php

Bundle::start('orchestra');

class PresentersExtensionTest extends Orchestra\Testable\TestCase {

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
		$memory     = Orchestra\Core::memory();
		$this->rows = new Laravel\Fluent(array(
			'handles' => 'foo'
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
	 * Test Orchestra\Presenter\Extension::form().
	 *
	 * @test
	 */
	public function testInstanceOfExtensionForm()
	{
		$stub = Orchestra\Presenter\Extension::form('foo', $this->rows);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('Orchestra\Form', $stub);
		$this->assertEquals(Orchestra\Form::of('orchestra.extension: foo'), $stub);
		$this->assertInstanceOf('Hybrid\Form\Grid', $grid);
	}
}