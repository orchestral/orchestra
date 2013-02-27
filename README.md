Orchestra Platform Bundle for Laravel
==============

Provide a solid base off of which to build your new web applications. It's not a CMS. Instead, it's a springboard to build off of with many of the tools you wish you had on projects but never took the time to build. 
Orchestra Platform is what Bonfire is for CodeIgniter.

[![Build Status](https://secure.travis-ci.org/orchestral/orchestra.png?branch=master)](http://travis-ci.org/orchestral/orchestra)

## Installation

Orchestra Platform is best installed in a clean Laravel environment, due to the facts it require to create `users` table and administrator account during installation.

### Installation with Laravel Artisan

Orchestra Platform can be downloaded directly from Artisan using the following commands:

	php artisan bundle:install orchestra

#### Bundle Registration

In the `application/bundles.php` file, add the following entries:

	'orchestra' => array('auto' => true, 'handles' => 'orchestra'),

> **Note on Orchestra**, you can change `handles` value to anything unique, such as `admin` for example.

#### Publish Bundle Asset

Before running the Setup instruction, run the following from Artisan to publish all related assets for Orchestra Platform:

	php artisan bundle:publish

## Setup

Now navigate to your Orchestra Platform handles URL in a web browser, you should see an installation page. Do remember to change the URL if you use a different handles value.

Please ensure that your configuration is correct, Orchestra Platform will utilize configuration from `application/config` folder to make the process stream-less with your other application (or bundles).

- Create a `User` model, which extends `Orchestra\Model\User` model.
- Update `application/config/database.php` and ensure your database connection is properly set up.
- Update `application/config/auth.php`.
	- Orchestra only supports Eloquent auth driver at the moment.
	- Select your `User` model, or change it to `Orchestra\Model\User`.

Complete the installation wizard, a simple 3 step installation process.

## Orchestra Platform Documentation

Orchestra Platform Bundle come with an offline documentation, to view this please download and enable `bundocs` bundle, see [Bundocs Bundle](http://bundles.laravel.com/bundle/bundocs) for more detail.

## Contributors

* [Mior Muhammad Zaki](http://git.io/crynobone)

## License

	The MIT License

	Copyright (C) 2012 by Mior Muhammad Zaki <http://git.io/crynobone>

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
