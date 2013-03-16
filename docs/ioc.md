# Injection of Container in Orchestra Platform

Other than `Event`, Orchestra Platform also utilize a number of IoC Container to add flexibility to your application.

<a name="mailer"></a>
## Mailer

By default Orchestra Platform would use Messages bundle, if you find a need to replace this dependency, simply replace the default IoC.

	IoC::register('orchestra.mailer', function ($from = true)
	{
		// replace with your implementation.
	});

<a name="memory"></a>
## Memory

`Orchestra\Memory` allows database configuration to be easily extends throughout Orchestra Platform. By default, we are using `Orchestra\Memory\Fluent` instance, but this is easily extends or replace if you find the need, for instance using redis instead of the default.

	Orchestra\Memory::extend('redis', function ($name, $config)
	{
		return new YourRedisMemory($name, $config);
	});
	
	IoC::register('orchestra.memory', function ()
	{
		return Orchestra\Memory::make('redis.orchestra_options');
	});

<a name="themes"></a>
## Themes

`Orchestra\Theme` dependency could also be replace if you need to.

	IoC::singleton('orchestra.theme: backend', function ()
	{
		// change your backend theme.
	});
	
	IoC::singleton('orchestra.theme: frontend', function ()
	{
		// change your frontend theme.
	});

	
