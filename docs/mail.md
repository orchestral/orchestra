# Orchestra Mail

By now, everyone would be loving Laravel 4 Mail class. Why wait when you can 
have it today with Orchestra Platform.

	Orchestra\Mail::send('mail.newsletter', compact('content'), function ($mail)
	{
		$mail->to('test@example.com')
			->subject('This is just a test.')
			->send();
	});