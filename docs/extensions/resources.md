# Resources for Extension

## Table of Content

- [Introduction](#introduction)
- [Register A Resource](#register)
- [Register Child Resources](#register-child)
- [Returning Response from Resources](#returning-response)

<a href="#introduction"></a>
## Introduction

Resources for Extension offer more control to developer to create application on top of Orchestra Administrator Interface. The idea is to 
allow controllers to be map to specific URL in Orchestra Administrator Interface instead of just pages.

<a href="#register"></a>
## Register A Resource

Normally we would identify a bundle to a resource for ease of use, however Orchestra still allow a single bundle to register multiple resources 
if such requirement is needed.

	$oneauth = Orchestra\Resources::make('oneauth', array(
		'name' => 'OneAuth',
		'uses' => 'oneauth::api.home', 
		// this would be equivalent of using 
		// Controller::call('oneauth::api.home')
	));

Orchestra Administrator Interface now would display a new tab next to Extension, and you can now navigate to available resources.

<a href="#register-child"></a>
## Register Child Resources

A single resource might require multiple actions (or controllers), we allow such feature to be used by assigning child resources.

	$oneauth->pages = 'oneauth.api.pages';

Child resources will not get a direct URL hyperlink from Orchestra Administrator Interface, but you can link any page to display the above 
resource using `handles('orchestra::resources/oneauth.pages')`.

<a href="#returning-response"></a>
## Returning Response from Resources

Controllers mapped as Orchestra Resources is no different from any other controller except the layout is using Orchestra UI. 
You can use `View`, `Response` and `Redirect` normally as you would without Orchestra integration.


