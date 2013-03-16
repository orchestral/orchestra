# Orchestra Platform Mail

By now, everyone would be loving Laravel 4 Mail class. Why wait when you can have it today with Orchestra Platform.

	Orchestra\Mail::send('mail.newsletter', compact('content'), function ($mail)
	{
		$mail->to('test@example.com')
			->subject('Your Awesome Subject.');
	});

> The Mail class is utilizing [Messages bundle](http://bundles.laravel.com/bundle/detail/Messages).

## Pretending

During development, you might not really need to send the actual e-mail, Orchestra Platform already create an option only pretend to send the e-mail.

	Orchestra\Mail::pretend(true);

## Alternative Method

Other than using `View` to make the body of an e-mail, you can also use IoC Container.

	$mail = IoC::resolve('orchestra.mailer');

	$mail->body('Your awesome content.')
		->subject('Your Awesome Subject.')
		->to('test@example.com')
		->send();
