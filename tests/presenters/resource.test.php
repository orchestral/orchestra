<?php namespace Orchestra\Tests\Presenters;

\Bundle::start('orchestra');

class ResourceTest extends \Orchestra\Testable\TestCase {

	/**
	 * Model instance.
	 *
	 * @var array
	 */
	protected $model = null;
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

		$this->model = array(
			'foo' => new \Laravel\Fluent(array(
				'visible' => true,
				'id'      => 'foo',
				'name'    => 'foo',
				'uses'    => 'orchestra::foo',
				'childs'  => array()
			))
		);

		$this->user = \Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->model);
		unset($this->user);

		parent::tearDown();
	}

	/**
	 * Test instanceof Orchestra\Presenter\Resource::table()
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfResourceTable()
	{
		$this->be($this->user);
		$stub = \Orchestra\Presenter\Resource::table($this->model);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Table', $stub);
		$this->assertEquals(\Orchestra\Table::of('orchestra.resources: list'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid);

		ob_start();
		echo $stub->render();
		$content = ob_get_contents();
		ob_end_clean();

		$this->assertContains(handles('orchestra::resources/foo'), 
			$content);
	}
}