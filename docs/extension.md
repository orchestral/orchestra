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

## Enabling an extension

Extensions will be manage by Orchestra Administrator Interface. Login as an administrator and go to **Extensions** on the top navigation.

Few things to consider:

- Only activated extensions will be run on runtime.
- Orchestra will start bundle which is activated as extensions.