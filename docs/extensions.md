# Extensions

## Table of Content

* [Introduction](#introduction)
* [Convert a Bundle](#convert-to-extension)
* [Enabling an Extension](#enable-extension)
* [Extension Start File](#start-file)
* [Configuring an Extension](#configure-extension)
	- [Disable configuration](#disable-configure-extension)
	- [Dependencies Management](#dependency-extension)

<a name="introduction"></a>
## Introduction

Extension may contain widget or resources (components) to be added for Orchestra Platform. By principle Extension in Orchestra Platform is a bundle except that Orchestra will manage setup process:

* Migration for bundle
* Publish asset for bundle

<a name="convert-to-extension"></a>
## Convert a Bundle

The process is simple, an extension is a Bundle except that first it need to have a definition file. The definition file will be stored in `bundles/bundle-name/orchestra.json`, this tell Orchestra Platform to handle the bundle as an extension. Here's an example of Definition File for OneAuth extension.

	{
		"name"        : "OneAuth",
		"description" : "OAuth, OAuth2 and OpenID Auth bundle for Laravel",
		"author"      : "Mior Muhammad Zaki",
		"url"         : "http://bundles.laravel.com/bundle/oneauth",
		"version"     : "0.1.0",
		"config"      : {
			"handles" : "oneauth"
		},
		"require"     : {}
	}

> The definition file can easily be created using `php artisan orchestra::toolkit definition`.

<a name="enable-extension"></a>
## Enabling an Extension

Extensions will be manage by Orchestra Platform Administrator Interface. Login as an 
administrator and go to **Extensions** on the top navigation.

Few things to consider:

* Only activated extensions will be run on runtime.
* Orchestra will start bundle which is activated as extensions.

<a name="start-file"></a>
## Extension Start File

Extension start file (optional) allow extension to run start script (as Laravel run bundles start.php file). The start file will be stored in `bundles/bundle-name/orchestra.php`. 

> The definition file can easily be created using `php artisan orchestra::toolkit start`.

What inside the file depends on how extension would interact with Orchestra Platform and this can be diverse depending on use cases.

Some examples:

* [OneAuth Start File](https://github.com/codenitive/laravel-oneauth/blob/master/orchestra.php)
* [Cello CMS Start File](https://github.com/orchestral/cello/blob/master/orchestra.php)
* [Robotix Start File](https://github.com/orchestral/robotix/blob/master/orchestra.php)

<a name="configure-extension"></a>
## Configuring an Extension

By default, administrator are able to configure any extension based on requirement of the application including `handles` value using Orchestra Platform Administrator Interface. This allow non-technical administrator to take charge of the application without having to understand any of the code.

To configure an extension, the extension need to be activated. Once this is done, all extension that allow configuration can be configured. Simply click on the extension name to navigate to the configuration page.

<a name="disable-configure-extension"></a>
### Disable configuration

Extension developer can disable configuration option by adding `"configurable" : false`, To do this edit your definition file.

	{
		/* ... */

		"config"      : {
			"handles"      : "oneauth",
			"configurable" : false
		}
	}

By doing so, Orchestra will take extension as it is and will not try to modify any of the configuration.

<a name="dependency-extension"></a>
### Dependencies Management with Extension

Managing dependencies has never been easier, extension developer can define and manage dependencies using `"require" : {}`, as you would using composer.

	{
		/* ... */

		"require" : {
			"cartie" : ">=0.1.0",
			"s3"     : "bundle"
		}
	}

An extension may also depends on certain bundle (which is not registered as an extension), in this case just define the minimum compatible version as `"bundle"` to indicate such dependencies.

> Credit to [@lordcoste](http://github.com/lordcoste) for the feature.

