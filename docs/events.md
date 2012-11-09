# Events

## Table of Contents
- [General Events](#general)
- [Installation Events](#installation)
- [User & Account Events](#user)

<a name="general"></a>
## General Events

### `orchestra.started`
Event fired when Orchestra is loaded by Laravel.

	Event::listen('orchestra.started', function ()
	{
		// Add an enquiry resource page
		$enquiry = Orchestra\Resources::make('contact', array(
			'name' => 'Contacts',
			'uses' => 'api.enquiries',
		));
	});

### `orchestra.started: backend`
Event fired when user is accessing Orchestra Administrator Interface.

	Event::listen('orchestra.started: backend', function ()
	{
		$asset = Asset::container('orchestra.backend');
		
		// Add Redactor CSS and JavaScript.
		$asset->script('redactor', 'bundles/cartie/vendor/redactor/redactor.js', array('jquery', 'bootstrap'));
		$asset->style('redactor', 'bundles/cartie/vendor/redactor/css/redactor.css', array('bootstrap'));
	});

### `orchestra.started: view`
Event fired when a View is from `Orchestra\View`, which mean it's possible to use View replacement using Theme.

	Event::listen('orchestra.started: view', function ()
	{
		Orchestra\Theme::map(array(
			'cartie::layout.main' => 'layout.main'
		));
	});
 
<a name="installation"></a>
## Installation Events

* `orchestra.install.schema`
* `orchestra.install.schema: users`
* `orchestra.install: user`
* `orchestra.install: acl`

<a name="user"></a>
## User & Account Events
* `orchestra.list: users`
* `orchestra.form: user`
* `orchestra.form: user.account`
* `orchestra.validate: users`
* `orchestra.validate: user.account`
* `orchestra.creating: users`
* `orchestra.creating: user.account`
* `orchestra.updating: users`
* `orchestra.updating: user.account`
* `orchestra.deleting: users`
* `orchestra.deleting: user.account`
* `orchestra.created: users`
* `orchestra.created: user.account`
* `orchestra.updated: users`
* `orchestra.updated: user.account`
* `orchestra.deleted: users`
* `orchestra.deleted: user.account`


