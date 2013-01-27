# Orchestra Platform Change Log

## Contents

- [v1.1.0](#v1.1.0)
- [v1.0.1](#v1.0.1)
- [v1.0.0](#v1.0.0)

<a name="v1.1.0"></a>
## v1.1.0

- Update vendors; Javie v0.1.1, Backbone.js v0.9.10 and Underscore.js v1.4.3.

<a name="v1.0.1"></a>
## v1.0.1

- Fixed undefined index when trying to get `Orchestra\Extension::option()`.

<a name="v1.0.0"></a>
## v1.0.0

- Add `Orchestra\Theme` and `Orchestra\View` and introduce [Melody Theme Manager](http://bundles.laravel.com/bundle/melody).
- Multiple event listeners to allow customizable on Orchestra including: installation process, CRUD for extension, users, account and settings configuration.
- Add FTP Publisher tool for web application hosted on shared hosting, where file permission is not have proper ownership.
- Add ability to add Resources from any extensions.
- Extension can check for dependencies/requirement before it can be activate, or deactivated.
- Add `Orchestra::VERSION` constant.
- Add `php artisan orchestra::toolkit` commandline task to helps developer create extension with ease.
- Ensure every part of Orchestra is using responsive design.
- Add unit testing and Travis-CI integration.
- Add `Orchestra\Testable\TestCase` and `Orchestra\Testable\Application` making functional/unit test an easy task for extensions development.
- Improvements to Role Based Access Control (RBAC) and introduce [Authorize Extension](http://bundles.laravel.com/bundle/authorize).
- Love Laravel 4 new Mail class, We have it too.
- Allow Memory instance to be defined from IoC, callable through `IoC::resolve('orchestra.memory')`.
- Easy to use @placeholder with Blade templating.
- Move `Orchestra\Table` and `Orchestra\Form` usage to presenters.
- Allow user registration on Orchestra Platform.
- Add `Orchestra\Installer\Requirement` class, optimize installer controller.
- Implement `Orchestra\Repository\User` to manage meta data for users.
- Add safe_mode to allow Orchestra Platform to be loaded without any Extension.
- Update to Twitter Bootstrap v2.2.2.