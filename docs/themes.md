# Theme with Orchestra Platform

## Table of Contents
- [Introduction](#introduction)
- [Enable Theme](#enable-theme)
- [Default Theme](#default-theme)
	- [Theme Manager](#manage-theme)
- [Anatomy of A Theme](#anatomy)
	- [Asset Folder](#assets)

<a name="introduction"></a>
## Introduction

Theme is Orchestra Platform works using a customize or fallback approach, the functionality evolves by modifying how `View` would resolve which file, in event where we find a view in the active theme folder, we would mark it as the intended view, either choose the default view given in the request.

This would allow extension (or even bundles) to have it's own set of style while developer can maintain a standardise overall design through out the project using a theme.

<a name="enable-theme"></a>
## Enable Theme in Your Application

Any extension start using this approach by using `Orchestra\View` instead of `\View`, or replacing the default facade alias for `View` in `application/config/application.php`.

	array(
		/* ... */

		'View' => 'Orchestra\\View',
	),

You would also need to register the classmap in `application/start.php` line 61.

	Autoloader::map(array(
		'Orchestra\View'  => path('bundle').'orchestra/libraries/view.php',
		'Base_Controller' => path('app').'controllers/base.php',
	));

<a name="default-theme"></a>
## Default Theme

By default, the selected theme is `default`, and located at `public/themes/default`, to change the selected theme, we recommend to use the [Melody Theme Manager](#manage-theme) Extension available for Orchestra Platform.

<a name="manage-theme"></a>
### Theme Manager

Choosing a theme is easily manage using [Melody Theme Manager](http://bundles.laravel.com/bundle/melody). You can replace `frontend` and `backend` theme separately. `backend` would be your Orchestra Platform theme and `frontend` would be your website including any other bundle or extensions enabled.

<a name="anatomy"></a>
## Anatomy of a Theme

The `DEFAULT_BUNDLE` views is accessible from the root path of your theme, while bundles can be accessible from `bundles/{bundle-name}` subfolder. So for example if your selected theme is `default`, and you plan to replace `home.index` and `foo::home.index` view. Only the following file would be needed.

* `public/themes/default/home/index.blade.php`
* `public/themes/default/bundles/foo/home.index.blade.php`
* `public/themes/default/theme.json`
* `public/themes/default/screenshot.png`

<a name="definition"></a>
### Theme Definition File

In order Melody Theme Manager to register and use your theme, please include a `screenshot.png` file with dimension of 300px*225px and a definition file `theme.json`

	{
		"name"        : "Default Theme",
		"description" : "Default Theme",
		"author"      : "Mior Muhammad Zaki"
	}


<a name="assets"></a>
### Assets

You are free to maintain where assets is located inside the theme folder as it is under public folder. To access the asset file, you can use the following snippet.

	<script src="{{ Orchestra\Theme::resolve()->to('assets/js/script.js') }}">
	<!-- this would point to `http:://yourdomain.com/themes/default/assets/js/script.js` -->
