<?php

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

Blade::extend(function ($view)
{
	$pattern = '/(\s*)@placeholder\s?\(\s*(.*)\)/';

	return preg_replace($pattern, '$1<?php foreach (Orchestra\Widget::make("placeholder.".$2)->get() as $_placeholder_): echo value($_placeholder_->value ?:""); endforeach; ?>', $view);
});