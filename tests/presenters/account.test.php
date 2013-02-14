<?php

Bundle::start('orchestra');

class PresentersAccountTest extends Orchestra\Testable\TestCase {

	/**
	 * Test Orchestra\Presenter\Account::form().
	 *
	 * @test
	 */
	public function testInstanceOfAccountForm()
	{
		$stub = Orchestra\Presenter\Account::form(
			new Orchestra\Model\User,
			handles('orchestra::account')
		);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('Orchestra\Form', $stub);
		$this->assertEquals(Orchestra\Form::of('orchestra.account'), $stub);
		$this->assertInstanceOf('Hybrid\Form\Grid', $grid);
	}

	/**
	 * Test Orchestra\Presenter\Account::form_password().
	 *
	 * @test
	 */
	public function testInstanceOfEditPasswordForm()
	{
		$user = Orchestra\Model\User::find(1);

		$this->be($user);

		$stub = Orchestra\Presenter\Account::form_password(
			$user,
			handles('orchestra::account')
		);

		$refl = new \ReflectionObject($stub);
		$grid = $refl->getProperty('grid');
		$grid->setAccessible(true);
		$grid = $grid->getValue($stub);

		$this->assertInstanceOf('Orchestra\Form', $stub);
		$this->assertEquals(Orchestra\Form::of('orchestra.account: password'), $stub);
		$this->assertInstanceOf('Hybrid\Form\Grid', $grid);
	}
}