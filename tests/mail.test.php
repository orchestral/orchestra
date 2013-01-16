<?php

Bundle::start('orchestra');

class MailTest extends Orchestra\Testable\TestCase {

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

	/**
	 * Test instance of mailer with invalid view will throw an exception.
	 *
	 * @test
	 */
	public function testRegisterUserMailer()
	{
		$user   = Orchestra\Model\User::find(1);
		$data   = array(
			'password' => '123456',
			'user'     => $user,
			'site'     => 'Orchestra',
		);

		$mail = new Orchestra\Mail(
			'orchestra::email.credential.register', 
			$data, 
			function ($mail) 
			{
				$mail->send();
			}
		);

		$refl   = new \ReflectionObject($mail);
		$mailer = $refl->getProperty('mailer');
		$mailer->setAccessible(true);

		$this->assertInstanceOf('Orchestra\Mail', $mail);
		$this->assertInstanceOf('Orchestra\Testable\Mailer', $mailer->getValue($mail));
		$this->assertTrue($mailer->getValue($mail)->was_sent());
	}
}
