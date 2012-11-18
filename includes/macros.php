<?php

/*
|--------------------------------------------------------------------------
| HTML::title() macro
|--------------------------------------------------------------------------
|
| Page title macro helper.
|
*/

HTML::macro('title', function ($page_title)
{
	$memory     = Orchestra::memory();
	$site_title = $memory->get('site.name');
	$page_title = trim($page_title);

	if (empty($page_title)) return $site_title;

	return strtr($memory->get('site.format.title', ':page-title &mdash; :site-title'), array(
		":site-title" => $site_title,
		":page-title" => $page_title,
	));
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