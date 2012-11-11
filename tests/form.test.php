<?php

class FormTest extends PHPUnit_Framework_TestCase {
	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

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
