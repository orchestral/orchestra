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
