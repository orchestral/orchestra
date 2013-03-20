# Orchestra Platform Change Log

## Contents

* [Version 1.2](#v1.2)
 - [v1.2.0](#v1.2.0)
* [Version 1.1](#v1.1)
 - [v1.1.3](#v1.1.3)
 - [v1.1.2](#v1.1.2)
 - [v1.1.1](#v1.1.1)
 - [v1.1.0](#v1.1.0)
* [Version 1.0](#v1.0)
 - [v1.0.5](#v1.0.5)
 - [v1.0.4](#v1.0.4)
 - [v1.0.3](#v1.0.3)
 - [v1.0.2](#v1.0.2)
 - [v1.0.1](#v1.0.1)
 - [v1.0.0](#v1.0.0)

<a name="v1.2"></a>
## Version 1.2

<a name="v1.2.0"></a>
### v1.2.0

**ONGOING DEVELOPMENT**

 * Add `Orchestra\Fluent` alias from `Orchestra\Support\Fluent`.
 * Add `Orchestra\Decorator` alias from `Orchestra\Support\Decorator`.
 * Add `Orchestra\Site::localtime()` helper.
 * Add `Orchestra\Theme::start()` to replace `Orchestra\Core::appearance()`.
 * Add `Input::file()` support for `Orchestra\Form`.
 * Add `Orchestra\Mail::pretend()` helper during development process.
 * Multiple improvements to `Orchestra\Messages`.
 * Multiple improvements to templating system.
 * Rename `orchestra.backend: bottom` asset container as `orchestra.backend: footer`.
 * Replace `Orchestra\Support\HTML::compile_attributes()` to `Orchestra\Support\HTML::decorate()`.
 * Remove requirement to run `$mailer->send()` when using `Orchestra\Mail::send()`.

<a name="v1.1"></a>
## Version 1.1

<a name="v1.1.2"></a>
### v1.1.2

* Add `Orchestra\Support\Site` to replace usage of ugly `$_title_` and `$_description_` in Orchestra Platform.
* Add `Orchestra\Support\Messages::store()` method.
* Add option to escape user inserted data using `Orchestra\Support\Table`.

<a name="v1.1.1"></a>
### v1.1.1

* Add `Orchestra\Support\Fluent` to support using `$fluent->attributes` without conflicting with the internal property.
* Replace all `markup` method to `attributes` in `Orchestra\Support\Form` and `Orchestra\Support\Table`.
* Replace `Orchestra\Support\HTML::markup()` to `Orchestra\Support\HTML::compile_attributes()`.

<a name="v1.1.0"></a>
### v1.1.0

* Remove `ochestra_migrations` table, not to be implemented.
* Remove un-used `Orchestra\Response` alias.
* Remove Hybrid Bundle dependencies and move all requirement to `Orchestra\Support`.
* Update Assets:
  - Twitter Bootstrap v2.3.0.
  - Zurb Foundation v3.2.5.

<a name="v1.0"></a>
## Version 1.0

<a name="v1.0.5"></a>
### v1.0.5

* 100% code coverage.
* Fixed password wasn't required for create user from Orchestra Platform Administrator Panel.
* Add `eloquent.publishing: extension` event.

<a name="v1.0.4"></a>
### v1.0.4

* Namespace tests folder as `Orchestra\Tests`.
* Refactor Resources, Manage and Pages response.
* Add `Orchestra\Theme::to_asset()` for relative URL path.
* Update vendor assets:
  - Backbone.js v0.9.10
  - Underscore.js v1.4.3
* `Orchestra_Resources_Controller` to return more information to the view.

<a name="v1.0.3"></a>
### v1.0.3

* 90% code coverage.
* Add `IoC::register('hybrid.view')` to allow `Orchestra\View` to manage `Hybrid\Form` and `Hybrid\Table` rendering.
* Allow to edit `email.from` configuration, and use SMTP username as email address to avoid email send out from Orchestra Platform to be caught as spam.
* Edit Password to use `Orchestra\Presenter` and `Orchestra\Form`.

<a name="v1.0.2"></a>
### v1.0.2

* Fixed a bug where User can't be created on Postgres.
* Improved Travis-CI by testing on multiple database configuration:
  - MySQL
  - Postgres
  - SQLite
* Add code coverage report on Travis-CI.

<a name="v1.0.1"></a>
### v1.0.1

* Fixed undefined index when trying to get `Orchestra\Extension::option()`.
* Improvements to unit testing.
* Add language detection to fixed a bug where Home menu doesn't follow locale.
* Allow to use `child-of` in `Orchestra\Widget\Nesty`.
* Update Javie Client-side JavaScript Library to version 1.0.1.

<a name="v1.0.0"></a>
### v1.0.0

* Add `Orchestra\Theme` and `Orchestra\View` and introduce [Melody Theme Manager](http://bundles.laravel.com/bundle/melody).
* Multiple event listeners to allow customizable on Orchestra including: installation process, CRUD for extension, users, account and settings configuration.
* Add FTP Publisher tool for web application hosted on shared hosting, where file permission is not have proper ownership.
* Add ability to add Resources from any extensions.
* Extension can check for dependencies/requirement before it can be activate, or deactivated.
* Add `Orchestra::VERSION` constant.
* Add `php artisan orchestra::toolkit` commandline task to helps developer create extension with ease.
* Ensure every part of Orchestra is using responsive design.
* Add unit testing and Travis-CI integration.
* Add `Orchestra\Testable\TestCase` and `Orchestra\Testable\Application` making functional/unit test an easy task for extensions development.
* Improvements to Role Based Access Control (RBAC) and introduce [Authorize Extension](http://bundles.laravel.com/bundle/authorize).
* Love Laravel 4 new Mail class, We have it too.
* Allow Memory instance to be defined from IoC, callable through `IoC::resolve('orchestra.memory')`.
* Easy to use @placeholder with Blade templating.
* Move `Orchestra\Table` and `Orchestra\Form` usage to presenters.
* Allow user registration on Orchestra Platform.
* Add `Orchestra\Installer\Requirement` class, optimize installer controller.
* Implement `Orchestra\Repository\User` to manage meta data for users.
* Add safe_mode to allow Orchestra Platform to be loaded without any Extension.
* Update to Twitter Bootstrap v2.2.2.
