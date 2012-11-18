# Theme with Orchestra

## Table of Contents
- [Introduction](#introduction)
- [Enable Theme](#enable-theme)
- [Default Theme](#default-theme)
	- [Theme Manager](#manage-theme)
- [Anatomy of A Theme](#anatomy)
	- [Asset Folder](#assets)



<a name="introduction"></a>
## Introduction

Theme is Orchestra works using a customize or fallback approach, the functionality evolves by modifying how `View` would 
resolve which file, in event where we find a view in the active theme folder, we would mark it as the intended view, either 
choose the default view given in the request.

This would allow extension (or even bundles) to have it's own set of style while developer can maintain a standardise overall 
design through out the project using a theme.

<a name="enable-theme"></a>
## Enable Theme in Your Application

Any extension start using this approach by using `Orchestra\View` instead of `\View`, or replacing the default facade alias 
for `View` in `application/config/application.php`.

	array(
		/* ... */

		'View' => path('bundle').'orchestra'.DS.'libraries'.DS.'view'.EXT,
	),

<a name="default-theme"></a>
## Default Theme

By default, the selected theme is `default`, and located at `public/themes/default`.

<a name="manage-theme"></a>
### Theme Manager

Choosing a theme is easily manage using [Melody Theme Manager](http://bundles.laravel.com/bundle/melody).

<a name="anatomy"></a>
## Anatomy of a Theme

The `DEFAULT_BUNDLE` views is accessible from the root path of your theme, while bundles can be accessible 
from `bundles/{bundle-name}` subfolder.

<a name="assets"></a>
### Asset folders?

Developer are free to maintain where assets is located inside the theme folder as it is under public folder.
