# Credential Events

Using credential events you can add event whenever a user is logged in or logged out from Orchestra Platform.

## orchestra.auth: login

Listen to whenever a user logged-in to Orchestra Platform. For example OneAuth use the following:

	Event::listen('orchestra.auth: login', function ()
	{
		Event::fire('oneauth.sync', array(Auth::user()->id));
	});

## orchestra.auth: logout

Listen to whenever a user logged-out from Orchestra Platform.

	Event::listen('orchestra.auth: logout', function ()
	{
		Session::forget('oneauth');
	});