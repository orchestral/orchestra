# Helpers

Orchestra Platform includes a set of helpers function to help solves some of the frequent problem while developing on Laravel.

## Table of Contents
- [handles()](#handles)
- [locate()](#locate)
- [memorize()](#memorize)

<a name="handles"></a>
## handles()

Return handles configuration for a bundle to generate a full URL.

	echo handles('orchestra::users');

Above code would return `http://yoursite.com/orchestra/users`, however if your Orchestra Platform bundle configuration is set to use **admin** as the bundle handles, the same code would then return `http:://yoursite.com/admin/users`.

<a name="locate"></a>
## locate()

Return theme path location of a requested view, this would allow `Orchestra\Theme` to check for existent of theme file associated to the given path before fallback to default view.

	@layout(locate('layout.main'))

Above code would check for `public/themes/{theme-name}/layout/main.blade.php` before fallback to 
`application/views/layout/main.blade.php`.

<a name="memorize"></a>
## memorize()

Return memory configuration associated to the request, helper alias to `Orchestra::memory()->get()`.

	{{ memorize('site.name') }}
