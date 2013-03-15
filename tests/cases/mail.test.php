<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class MailTest extends \Orchestra\Testable\TestCase {

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\Mail::$pretending = false;
	}

	/**
	 * Test instance of mailer with invalid view will throw an exception.
	 *
	 * @expectedException \Exception
	 * @group core
	 * @group mail
	 */
	public function testInstanceOfMailerWithInvalidViewThrowsException()
	{
		$mailer = new \Orchestra\Mail(
			'orchestra::an.unknown.view',
			array(),
			function ($mail) {}
		);
	}

	/**
	 * Test using Orchestra\Mail.
	 *
	 * @test
	 * @group core
	 * @group mail
	 */
	public function testUsingMailer()
	{
		$user = \Orchestra\Model\User::find(1);
		$data = array(
			'password' => '123456',
			'user'     => $user,
			'site'     => 'Orchestra',
		);

		$mail = new \Orchestra\Mail(
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
		$view   = $refl->getProperty('view');

		$mailer->setAccessible(true);
		$view->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Mail', $mail);
		$this->assertTrue($mailer->getValue($mail)->was_sent($user->email));
		$this->assertInstanceOf('\Laravel\View', $view->getValue($mail));
	}

	/**
	 * Test using Orchestra\Mail::send().
	 *
	 * @test
	 * @group core
	 * @group mail
	 */
	public function testUsingMailSend()
	{
		$user = \Orchestra\Model\User::find(1);
		$data = array(
			'password' => '123456',
			'user'     => $user,
			'site'     => 'Orchestra',
		);

		$mail = \Orchestra\Mail::send(
			'orchestra::email.credential.register', 
			$data, 
			function ($mail) use ($data, $user)
			{
				$mail->subject(__('orchestra::email.credential.register', array('site' => $data['site']))->get())
					->to($user->email, $user->fullname);
			}
		);

		$this->assertInstanceOf('\Orchestra\Mail', $mail);
		$this->assertTrue($mail->was_sent($user->email));
	}

	/** 
	 * Test Orchestra\Mail::pretend() method.
	 *
	 * @test
	 * @group mail
	 */
	public function testPretendMethod()
	{
		\Orchestra\Mail::$pretending = false;

		\Orchestra\Mail::pretend(true);

		$this->assertTrue(\Orchestra\Mail::$pretending);
	}
}