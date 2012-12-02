# Events

Orchestra utilise `Event` class from Laravel to extends the functionality, without having to modify any of the code. 
Listed below are all the possible events that can be used with Orchestra Platform.

- [General Events](/bundocs/orchestra/events/general)
	- orchestra.started
	- orchestra.started: backend
	- orchestra.started: view
	- orchestra.done
	- orchestra.done: backend
- [Installation Events](/bundocs/orchestra/events/install)
	- orchestra.install.schema
	- orchestra.install.schema: users
	- orchestra.install: user
	- orchestra.install: acl
- [Credential Events](/bundocs/orchestra/events/credential)
	- orchestra.logged.in
	- orchestra.logged.out
- Extension Events
	- orchestra.form: extension.{name}
	- orchestra.saving: extension.{name}
	- orchestra.saved: extension.{name}
- Page Events
	- orchestra.pages: {name}.{action}
	- orchestra.manages: {name}.action}
- Manage User Events
	- `orchestra.list: users
	- orchestra.form: users
	- orchestra.validate: users
	- orchestra.creating: users
	- orchestra.updating: users
	- orchestra.deleting: users
	- orchestra.created: users
	- orchestra.updated: users
	- orchestra.deleted: users
- User Account Events
	- orchestra.form: user.account
	- orchestra.validate: user.account
	- orchestra.creating: user.account
	- orchestra.updating: user.account
	- orchestra.deleting: user.account
	- orchestra.created: user.account
	- orchestra.updated: user.account
	- orchestra.deleted: user.account
- Setting Events
	- orchestra.form: settings
	- orchestra.validate: settings
	- orchestra.saved: settings
