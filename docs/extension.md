# Extensions

Extension may contain widget or resources (components) to be added for Orchestra. By principle Extension in Orchestra is a bundle except that Orchestra will manage setup process:

- Migration for bundle
- Publish asset for bundle

## How to convert a bundle to extension?

The process is simple, an extension is a Bundle except that first it need to have a definition file. The definition file will be stored in `bundles/bundle-name/orchestra.json`. Here's an example of Definition File for OneAuth extension.

	{
		"name"        : "OneAuth",
		"description" : "OAuth, OAuth2 and OpenID Auth bundle for Laravel",
		"author"      : "Mior Muhammad Zaki",
		"url"         : "http://bundles.laravel.com/bundle/oneauth",
		"version"     : "0.1.0",
		"config"      : {
			"handles" : "oneauth"
		}
	}


## Enabling an Extension

Extensions will be manage by Orchestra Administrator Interface. Login as an administrator and go to **Extensions** on the top navigation.

Few things to consider:

- Only activated extensions will be run on runtime.
- Orchestra will start bundle which is activated as extensions.

## Extension Start File

Extension start file (optional) allow extension to run start script (as Laravel run bundles start.php file). The start file will be stored in `bundles/bundle-name/orchestra.php`. 

What inside the file depends on how extension would interact with Orchestra and this can be diverse depending on use cases.

Some examples:

- [OneAuth Start File](https://github.com/codenitive/laravel-oneauth/blob/master/orchestra.php)

## Configuring an Extension

By default, administrator are able to configure any extension based on requirement of the application including `handles` value using Orchestra Administrator Interface. This allow non-technical administrator to take charge of the application without having to understand any of the code.

To configure an extension, the extension need to be activated. Once this is done, all extension that allow configuration can be configured. Simply click on the extension name to navigate to the configuration page.

### Disable configuration

Extension developer can disable configuration option by adding `"configure" : false`, To do this edit your definition file.

	{
		"name"        : "OneAuth",
		"description" : "OAuth, OAuth2 and OpenID Auth bundle for Laravel",
		"author"      : "Mior Muhammad Zaki",
		"url"         : "http://bundles.laravel.com/bundle/oneauth",
		"version"     : "0.1.0",
		"config"      : {
			"handles"   : "oneauth",
			"configure" : false
		}
	}

By doing so, Orchestra will take extension as it is and will not try to modify any of the configuration.