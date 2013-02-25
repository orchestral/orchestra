<?php namespace Orchestra\Tests\Presenters;

\Bundle::start('orchestra');

class AccountTest extends \Orchestra\Testable\TestCase {

	/**
	 * User instance.
	 *
	 * @var  Orchestra\Model\User
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
	 * Test Orchestra\Presenter\Account::form().
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfAccountForm()
	{
		$stub = \Orchestra\Presenter\Account::form(
			new \Orchestra\Model\User,
			handles('orchestra::account')
		);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Form', $stub);
		$this->assertEquals(\Orchestra\Form::of('orchestra.account'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid);
	}

	/**
	 * Test Orchestra\Presenter\Account::form_password().
	 *
	 * @test
	 * @group presenter
	 */
	public function testInstanceOfEditPasswordForm()
	{
		$this->be($this->user);

		$stub = \Orchestra\Presenter\Account::form_password(
			$this->user,
			handles('orchestra::account')
		);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Form', $stub);
		$this->assertEquals(\Orchestra\Form::of('orchestra.account: password'), $stub);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid);
	}
}