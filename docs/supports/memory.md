# Memory Helper Class

`Orchestra\Memory` handle runtime configuration either using 'in memory' Runtime or database using Cache, Fluent Query Builder or Eloquent ORM.

## Make an instance

Below are list of possible configuration to use `Orchestra\Memory`:

	$runtime  = Orchestra\Memory::make('runtime');
	$fluent   = Orchestra\Memory::make('fluent');
	$eloquent = Orchestra\Memory::make('eloquent'); 
	$cache    = Orchestra\Memory::make('cache');

However, most of the time you wouldn't need to have additional memory instance other than the one initiated by Orchestra Platform which is using `orchestra_options` table.

	$orchestra = Orchestra\Core::memory();

## Storing Items

Storing items in the cache is simple. Simply call the **put** method:

	$orchestra->put('site.author', 'Taylor');

The first parameter is the **key** to the config item. You will use this key to retrieve the item from the config. The second parameter is the **value** of the item. 

## Retrieving Items

Retrieving items from the config is even more simple than storing them. It is done using the **get** method. Just mention the key of the item you wish to retrieve:

	$name = $orchestra->get('site.author');

By default, NULL will be returned if the cached item has expired or does not exist. However, you may pass a different default value as a second parameter to the method:

	$name = $orchestra->get('site.author', 'Fred');

Now, "Fred" will be returned if the "name" cache item has expired or does not exist.

## Removing Items

Need to get rid of a cached item? No problem. Just mention the name of the item to the **forget** method:

	$orchestra->forget('site.author');
