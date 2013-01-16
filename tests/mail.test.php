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
	 * Test using Orchestra\Mail.
	 *
	 * @test
	 */
	public function testUsingMailer()
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
			function ($mail) use ($data, $user)
			{
				$mail->subject(__('orchestra::email.credential.register', array('site' => $data['site']))->get())
					->to($user->email, $user->fullname)
					->send();
			}
		);

		$refl   = new \ReflectionObject($mail);
		$mailer = $refl->getProperty('mailer');
		$mailer->setAccessible(true);

		$this->assertInstanceOf('Orchestra\Mail', $mail);
		$this->assertInstanceOf('Orchestra\Testable\Mailer', $mailer->getValue($mail));
		$this->assertTrue($mailer->getValue($mail)->was_sent($user->email));
	}

	/**
	 * Test using Orchestra\Mail::send().
	 *
	 * @test
	 */
	public function testUsingMailSend()
	{
		$user   = Orchestra\Model\User::find(1);
		$data   = array(
			'password' => '123456',
			'user'     => $user,
			'site'     => 'Orchestra',
		);

		$mail = Orchestra\Mail::send(
			'orchestra::email.credential.register', 
			$data, 
			function ($mail) use ($data, $user)
			{
				$mail->subject(__('orchestra::email.credential.register', array('site' => $data['site']))->get())
					->to($user->email, $user->fullname)
					->send();
			}
		);
		
		$this->assertInstanceOf('Orchestra\Testable\Mailer', $mail);
		$this->assertTrue($mail->was_sent($user->email));
	}
}
