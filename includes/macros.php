<?php

/*
|--------------------------------------------------------------------------
| HTML::title() macro
|--------------------------------------------------------------------------
|
| Page title macro helper.
|
*/

HTML::macro('title', function ()
{
	$memory     = Orchestra::memory();
	$site_title = $title = $memory->get('site.name');
	$page_title = trim(Orchestra\Site::get('title', ''));
	$format     = $memory->get('site.format.title', ':page-title &mdash; :site-title');

	if ( ! empty($page_title)) 
	{
		$title = strtr($format, array(
			":site-title" => $site_title,
			":page-title" => $page_title,
		));
	}

	return Orchestra\HTML::create('title', $title);
});

/*
|--------------------------------------------------------------------------
| Blade extend for @title and @description
|--------------------------------------------------------------------------
*/

Blade::extend(function ($view)
{
	$pattern     = '/(\s*)@(title|description)\s?/';
	$replacement = '$1<?php echo Orchestra\Site::get("$2"); ?>';

	return preg_replace($pattern, $replacement, $view);
});

/*
|--------------------------------------------------------------------------
| Blade extend for @placeholder
|--------------------------------------------------------------------------
|
| Placeholder is Orchestra version of widget for theme.
|
*/

Blade::extend(function ($view)
{
	$pattern     = '/(\s*)@placeholder\s?\(\s*(.*)\)/';
	$replacement = '$1<?php foreach (Orchestra\Widget::make("placeholder.".$2)->get() as $_placeholder_): echo value($_placeholder_->value ?:""); endforeach; ?>';

	return preg_replace($pattern, $replacement, $view);
});

/*
|--------------------------------------------------------------------------
| Decorator Macro for Navbar
|--------------------------------------------------------------------------
*/

Orchestra\Decorator::macro('navbar', function ($navbar)
{
	return Orchestra\View::make('orchestra::layout.widgets.navbar', compact('navbar'));
});
