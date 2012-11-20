<?php

/*
|--------------------------------------------------------------------------
| IMPORTANT NOTE!
|--------------------------------------------------------------------------
|
| Edit this configuration before installing Orchestra, otherwise use the
| Orchestra > Settings configuration page to change any of following options.
|
 */

return array(

	/*
	|----------------------------------------------------------------------
	| Default Swift Mailer Transport
	|----------------------------------------------------------------------
	|
	| The name of your default Swift Mailer Transport. This transport will
	| used as the default for all mailing operations unless a different name
	| is given when performing said operation. This transport name should be
	| listed in the array of transports below.
	|
	*/

	'default' => 'mail',

	/*
	|----------------------------------------------------------------------
	| Swift Mailer Transports
	|----------------------------------------------------------------------
	|
	| Below is the configuration for each of the Swift Mailer transports.
	| For more information refer to:
	|
	|	http://swiftmailer.org/docs/sending.html
	|
	*/

	'transports' => array(

		'smtp' => array(
			'host'       => 'smtp.example.com',
			'port'       => 25,
			'username'   => 'username',
			'password'   => 'password',
			'encryption' => null,
		),

		'sendmail' => array(
			'command' => '/usr/sbin/sendmail -bs',
		),

		'mail',

	),
);
