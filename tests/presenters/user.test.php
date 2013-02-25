<?php namespace Orchestra\Tests\Presenters;

\Bundle::start('orchestra');

class UserTest extends \Orchestra\Testable\TestCase {

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
	 * Test instanceof Orchestra\Presenter\User::table()
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfUserTable()
	{
		$this->be($this->user);

		$user = \Orchestra\Model\User::paginate(5);
		$stub = \Orchestra\Presenter\User::table($user);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Table', $stub);
		$this->assertEquals(\Orchestra\Table::of('orchestra.users'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid);
	}

	/**
	 * Test instanceof Orchestra\Presenter\User::table_action()
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfUserTableAction()
	{
		$foouser = \Orchestra\Model\User::where_email('member@orchestra.com')->first();

		$this->be($this->user);

		$user   = \Orchestra\Model\User::paginate(5);
		$stub   = \Orchestra\Presenter\User::table($user); 
		$output = \Orchestra\Presenter\User::table_actions($stub);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertNull($output);
		$this->assertInstanceOf('\Orchestra\Table', $stub);
		$this->assertEquals(\Orchestra\Table::of('orchestra.users'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $grid);

		ob_start();
		echo $stub->render();
		$content = ob_get_contents();
		ob_end_clean();

		$admin  = \Orchestra\Model\Role::admin();

		$this->assertContains($this->user->fullname, $content);
		$this->assertContains($this->user->email, $content);
		$this->assertContains('<span class="label label-info" role="role">'.$admin->name.'</span>', 
			$content);
		$this->assertContains(handles('orchestra::users/view/1'), 
			$content);
		$this->assertContains(handles('orchestra::users/view/'.$foouser->id), 
			$content);
	}

	/**
	 * Test instanceof Orchestra\Presenter\User::form()
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfUserForm()
	{
		$this->be($this->user);
		$stub = \Orchestra\Presenter\User::form($this->user);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Support\Form', $stub);
		$this->assertEquals(\Orchestra\Form::of('orchestra.users'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid);

		ob_start();
		echo $stub->render();
		$content = ob_get_contents();
		ob_end_clean();

		$admin  = \Orchestra\Model\Role::admin();
		$member = \Orchestra\Model\Role::member();

		$this->assertContains($this->user->fullname, $content);
		$this->assertContains($this->user->email, $content);
		$this->assertContains($admin->name, $content);
		$this->assertContains($member->name, $content);
	}
}