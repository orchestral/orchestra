# Toolkit

Toolkit for Orchestra Platform is a collection of task to assist developer to bootstrap extension development. 

## Table of Content

* [Version Command](#version)
* [Installer Command](#installer)
* [Initiate a New Extension Command](#init)
	- [Create the definition file](#definition)
	- [Create the start file](#start)

<a name="version"></a>
## Version Command

You can get current Orchestra Platform version using the following command.

	$ php artisan orchestra::toolkit version

<a name="installer"></a>
## Installer Command

Create `application/orchestra/installer.php` with just a command.

	$ php artisan orchestra::toolkit installer

`--bundle` option is not available for this command since Orchestra Platform would only allow application to have the option.

<a name="init"></a>
## Initiate a New Extension Command

This command would create `application/orchestra.json` and `application/orchestra.php` file.

	$ php artisan orchestra::toolkit init
	
You can also do the same to bundle.

	$ php artisan orchestra::toolkit init --bundle=foo

<a name="definition"></a>
### Create the definition file

Alternatively, if you just need the definition file.

	$ php artisan orchestra::toolkit definition

<a name="start"></a>	
### Create the start file

You can also just create the start file.

	$ php artisan orchestra::toolkit start
	




