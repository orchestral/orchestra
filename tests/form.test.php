<?php

Bundle::start('orchestra');

class FormTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test Orchestra\Form is an instance of Hybrid\Form
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$form = Orchestra\Form::make(function () {});
		$this->assertInstanceOf('Hybrid\Form', $form);
	}
}
