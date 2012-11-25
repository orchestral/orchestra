<?php

class MailTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test instance of mailer with invalid view will throw an exception.
	 *
	 * @expectedException \Exception
	 */
	public function testIstanceOfMailerWithInvalidViewThrowException()
	{
		$mailer = new Orchestra\Mail(
			'orchestra::an.unknown.view',
			array(),
			function ($mail) {}
		);
	}
}
