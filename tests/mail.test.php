<?php

Bundle::start('orchestra');

class MailTest extends PHPUnit_Framework_TestCase {

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
