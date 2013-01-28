# Pages for Extension

## Table of Content
- [Introduction](#introduction)
- [Add a Page for Administrators](#manage)
- [Add a Page for Users](#page)

<a name="introduction"></a>
## Introduction

Orchestra Platform wouldn't be useful if you can't create a custom page on Orchestra Platform Administrator Panel. For this purpose, we introduce two event listener to add dynamic page for either user and administrator.

<a name="manage"></a>
## Add a Page for Administrators

Use `"orchestra.manages: ..."` event listener to add a custom page only accessible by administrator user account. Here's an example of a custom manage page for oneauth.

	<?php

	Event::listen('orchestra.started: backend', function ()
	{
		// Add a custom menu
		$menu = Orchestra\Core::menu();

		$menu->add('hello', 'after:home')
			->title('Hello World')
			->link(handles('orchestra::manages/oneauth.hello'));

		Event::listen('orchestra.manages: oneauth.hello', function ()
		{
			return 'Hello world';
		});
	});

<a name="page"></a>
## Add a Page for Users

Use `"orchestra.pages: ..."` event listener to add a custom page viewable by all logged user, it work similarly as [Add a Page for Administrator](#manage).

	<?php

	Event::listen('orchestra.started: backend', function ()
	{
		// Add a custom menu
		$menu = Orchestra\Core::menu();

		$menu->add('hello', 'after:home')
			->title('Hello World')
			->link(handles('orchestra::pages/oneauth.hello'));

		Event::listen('orchestra.pages: oneauth.hello', function ()
		{
			return 'Hello world';
		});
	});
