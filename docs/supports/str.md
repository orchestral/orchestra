# Str Helper Class

`Orchestra\Support\Str` is properly the few internal API class that you might use directly. It extends `Laravel\Str` and offer improvement when dealing with **blob** database record from PostgreSQL.

	$str = Orchestra\Support\Str::stream_get_contents($blob);
	
Unliked other database driver, when using **blob** with PostgreSQL, the return value from database is a stream instead of string, using above helper method help convert it properly back to string.