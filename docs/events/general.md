# General Events

Basic Events that would be use by Orchestra Platform.

## orchestra.started

Event fired when Orchestra Platform is loaded by Laravel.

	Event::listen('orchestra.started', function ()
	{
		// Add an enquiry resource page
		$enquiry = Orchestra\Resources::make('contact', array(
			'name' => 'Contacts',
			'uses' => 'api.enquiries',
		));
	});

## orchestra.started: backend

Event fired when user is accessing Orchestra Platform Administrator Interface.

	Event::listen('orchestra.started: backend', function ()
	{
		$asset = Asset::container('orchestra.backend');
		
		// Add Redactor CSS and JavaScript.
		$asset->script('redactor', 'bundles/cartie/vendor/redactor/redactor.js', array('jquery', 'bootstrap'));
		$asset->style('redactor', 'bundles/cartie/vendor/redactor/css/redactor.css', array('bootstrap'));
	});

## orchestra.started: view

Event fired when a View is from `Orchestra\View`. Which make it possible to use View replacement using Theme.

	Event::listen('orchestra.started: view', function ()
	{
		Orchestra\Theme::map(array(
			'cartie::layout.main' => 'layout.main'
		));
	});

## orchestra.done

Event fire when `laravel.done` execute `Orchestra\Core::shutdown()`. If an extension would need to trigger anything right before request is output to end-user this would be a suitable event:

	Event::listen('orchestra.done', function()
	{
		// some awesome implementation.
	}); 

## orchestra.done: backend

Event fire after a request is handled by `Orchestra\Controller`, this is being used internally to generate Orchestra Platform Administrator Interface menu.

	Event::listen('orchestra.done: backend', function()
	{
		$menu = Orchestra\Core::menu();
		$menu->add('website')
			->link('/')
			->title('&mdash; Visit Website');
	});