<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class FormTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test Orchestra\Form is an instance of Orchestra\Support\Form
	 *
	 * @test
	 */
	public function testInstanceOfForm()
	{
		$form = \Orchestra\Form::make(function () {});
		$refl = new \ReflectionObject($form);
		$grid = $refl->getProperty('grid');
		
		$grid->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Form', $form);
		$this->assertInstanceOf('\Orchestra\Support\Form\Grid', $grid->getValue($form));
	}
}
