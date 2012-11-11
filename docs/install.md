# Installation

Orchestra Platform is best installed in a clean Laravel environment, due to the facts it require to create `users` table and administrator account during installation.

<a name="download"></a>
## Installation with Laravel Artisan

Orchestra can be downloaded directly from Artisan using the following commands:

	php artisan bundle:install orchestra

### Bundle Registration

In the `application/bundles.php` file, add the following entries:

	'orchestra' => array('auto' => true, 'handles' => 'orchestra'),
	'hybrid',

> **Note on Orchestra**, you can change `handles` value to anything unique, such as `admin` for example.

> **Note on Hybrid**, registering Hybrid bundle is optional, but allow you to upgrade Orchestra and Hybrid with just a single `php artisan bundle:upgrade`.

### Publish Bundle Asset

Before running the Setup instruction, run the following from Artisan to publish all related assets for Orchestra:

	php artisan bundle:publish

<a name="setup"></a>
# Setup

Now navigate to your Orchestra handles URL in a web browser, you should see an installation page. Do remember to change the URL if you use a different handles value.

Please ensure that your configuration is correct, Orchestra will utilize configuration from `application/config` folder to make the process stream-less with your other application (or bundles).

- Create a `User` model, which extends `Orchestra\Model\User` model.
- Update `application/config/database.php` and ensure your database connection is properly set up.
- Update `application/config/auth.php`.
	- Orchestra only supports Eloquent auth driver at the moment.
	- Select your `User` model, or change it to `Orchestra\Model\User`.

Complete the installation wizard, a simple 3 step installation process.