# Events

Orchestra utilise `Event` class from Laravel to extends the functionality, without having to modify any of the code.

## Table of Contents
- [General Events](#general)
- [Installation Events](#installation)
- [Credential Events](#credential)
- [Extension Events](#extension)
- [Page Events](#page)
- [Manage User Events](#user)
- [User Account Events](#account)
- [Setting Events](#setting)


<a name="general"></a>
## General Events

* `orchestra.started`
* `orchestra.started: backend`
* `orchestra.started: view`
* `orchestra.done`
* `orchestra.done: backend`
 
<a name="installation"></a>
## Installation Events

* `orchestra.install.schema`
* `orchestra.install.schema: users`
* `orchestra.install: user`
* `orchestra.install: acl`

<a name="credential"></a>
## Credential Events

* `orchestra.logged.in`
* `orchestra.logged.out`

<a name="extension"></a>
## Extension Events

* `orchestra.form: extension.{name}`
* `orchestra.saving: extension.{name}`
* `orchestra.saved: extension.{name}`

<a name="page"></a>
## Page Events

* `orchestra.pages: {name}.{action}`
* `orchestra.manages: {name}.action}`

<a name="user"></a>
## Manage User Events

* `orchestra.list: users`
* `orchestra.form: users`
* `orchestra.validate: users`
* `orchestra.creating: users`
* `orchestra.updating: users`
* `orchestra.deleting: users`
* `orchestra.created: users`
* `orchestra.updated: users`
* `orchestra.deleted: users`

<a name="account"></a>
## User Account Events

* `orchestra.form: user.account`
* `orchestra.validate: user.account`
* `orchestra.creating: user.account`
* `orchestra.updating: user.account`
* `orchestra.deleting: user.account`
* `orchestra.created: user.account`
* `orchestra.updated: user.account`
* `orchestra.deleted: user.account`

<a name="setting"></a>
## Setting Events

* `orchestra.form: settings`
* `orchestra.validate: settings`
* `orchestra.saved: settings`



