# General Events

## `orchestra.started`
Event fired when Orchestra is loaded by Laravel.

	Event::listen('orchestra.started', function ()
	{
		// Add an enquiry resource page
		$enquiry = Orchestra\Resources::make('contact', array(
			'name' => 'Contacts',
			'uses' => 'api.enquiries',
		));
	});

## `orchestra.started: backend`
Event fired when user is accessing Orchestra Administrator Interface.

	Event::listen('orchestra.started: backend', function ()
	{
		$asset = Asset::container('orchestra.backend');
		
		// Add Redactor CSS and JavaScript.
		$asset->script('redactor', 'bundles/cartie/vendor/redactor/redactor.js', array('jquery', 'bootstrap'));
		$asset->style('redactor', 'bundles/cartie/vendor/redactor/css/redactor.css', array('bootstrap'));
	});

## `orchestra.started: view`
Event fired when a View is from `Orchestra\View`. Which make it possible to use View replacement using Theme.

	Event::listen('orchestra.started: view', function ()
	{
		Orchestra\Theme::map(array(
			'cartie::layout.main' => 'layout.main'
		));
	});