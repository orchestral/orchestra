# Auth Helper Class

`Orchestra\Auth` extends the functionality of `Laravel\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Orchestra\Acl` to manage application Access Control List (ACL).

Retrieve user's roles is as simple as:

	$roles = Orchestra\Auth::roles();

To check if user has a role.

	if (Orchestra\Auth::is(array('admin')))
	{
		echo "Is an admin";
	}