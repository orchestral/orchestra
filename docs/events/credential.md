# Credential Events

Using credential events you can add event whenever a user is logged in or logged out from Orchestra.

## orchestra.logged.in

Listen to whenever a user logged-in to Orchestra. For example OneAuth use the following:

	Event::listen('orchestra.logged.in', function ()
	{
		Event::fire('oneauth.sync', array(Auth::user()->id));
	});

## orchestra.logged.out

Listen to whenever a user logged-out from Orchestra.

	Event::listen('orchestra.logged.out', function ()
	{
		Session::forget('oneauth');
	});