<?php

return array(
	'forgot' => array(
		'subject' => "[:site] Reset Your Password",
		'message' => "Hello :fullname,

We got a request to reset your password. If this was a mistake, just ignore this email and nothing will happen.

To reset your password, please proceed to :url and reset your password.",
	),
	'reset' => array(
		'subject' => "[:site] Your New Password",
		'message' => "Password has been reset for :fullname

Please login with your temporary password: :password 

Once logged, please change your password.",
	)
);