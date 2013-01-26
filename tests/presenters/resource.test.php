<?php

Bundle::start('orchestra');

class PresentersResourceTest extends Orchestra\Testable\TestCase {

	/**
	 * Model instance.
	 *
	 * @var array
	 */
	protected $model = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->model = array(
			'foo' => new Laravel\Fluent(array(
				'visible' => true,
				'name'    => 'foo',
				'uses'    => 'orchestra::foo',
				'childs'  => array()
			))
		);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->model);

		parent::tearDown();
	}

	/**
	 * Test instanceof Orchestra\Presenter\Resource::table()
	 *
	 * @test
	 */
	public function testInstanceOfResourceTable()
	{
		$stub = Orchestra\Presenter\Resource::table($this->model);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('Orchestra\Table', $stub);
		$this->assertInstanceOf('Hybrid\Table\Grid', $grid);
	}


}