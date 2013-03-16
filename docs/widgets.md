# Widgets in Orchestra

Widget allow you to manage widgetize actions in Orchestra Platform. By default Orchestra Platform provides the following widgets:

* **Menu** - Manage menu in Orchestra Platform
* **Pane** - Manage dashboard items in Orchestra Platform
* **Placeholder** - Manage sidebar widgets in Orchestra Platform.

## Example

	$p = Orchestra\Widget::make('placeholder.orchestra.helps');
	$p->add('demo', function ()
	{
		// you can return a string or a View.
		return View::make('placeholders.orchestra-helps');
	});