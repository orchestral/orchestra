## Helpers

Orchestra includes a set of helpers function to help solves some of the frequent problem while developing on Laravel.

## handles()

Return handles configuration for a bundle to generate a full URL.

	echo handles('orchestra::users');

Above code would return `http://yoursite.com/orchestra/users`, however if your orchestra bundle configuration is set to use **admin** 
as the bundle handles, the same code would then return `http:://yoursite.com/admin/users`.