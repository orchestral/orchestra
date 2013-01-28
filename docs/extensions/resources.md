# Resources for Extension

## Table of Content

- [Introduction](#introduction)
- [Add a Resource](#register)
- [Add Child Resources](#register-child)
- [Returning Response](#returning-response)

<a name="introduction"></a>
## Introduction

Resources for Extension offer more control to developer to create application on top of Orchestra Platform Administrator Interface. The idea is to allow controllers to be map to specific URL in Orchestra Platform Administrator Interface instead of just pages.

<a name="register"></a>
## Add a Resource

Normally we would identify a bundle to a resource for ease of use, however Orchestra Platform still allow a single bundle to register multiple resources if such requirement is needed.

	$oneauth = Orchestra\Resources::make('oneauth', array(
		'name' => 'OneAuth',
		'uses' => 'oneauth::api.home', 
		// this would be equivalent of using 
		// Controller::call('oneauth::api.home')
	));

Orchestra Platform Administrator Interface now would display a new tab next to Extension, and you can now navigate to available resources.

<a name="register-child"></a>
## Add Child Resources

A single resource might require multiple actions (or controllers), we allow such feature to be used by assigning child resources.

	$oneauth->pages = 'oneauth::api.pages';

Child resources will not get a direct URL hyperlink from Orchestra Platform Administrator Interface, but you can link any page to display the above resource using `handles('orchestra::resources/oneauth.pages')`.

<a name="returning-response"></a>
## Returning Response

Controllers mapped as Orchestra Platform Resources is no different from any other controller except the layout is using Orchestra UI. You can use `View`, `Response` and `Redirect` normally as you would without Orchestra Platform integration.


