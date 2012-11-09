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
		// Add a resource page
		$enquiry = Orchestra\Resources::make('contact', array(
			'name' => 'Contacts',
			'uses' => 'api.enquiries',
		));
	});

* `orchestra.started: backend`
* `orchestra.started: view`

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


