# HTML Helper Class

## Create HTML Tag

Create a HTML tag from within your libraries/extension using following code:

	{{ Orchestra\Support\HTML::create('p', 'Some awesome information') }}

Customize the HTML attibutes by adding third parameter.

	{{ Orchestra\Support\HTML::create('p', 'Another awesomeness', array('id' => 'foo')) }}

## Decorate HTML

Decorate method allow developer to define HTML attributes collection as `HTML::attributes` method, with the addition of including default attributes array as second parameter.

	$attributes = Orchestra\Support\HTML::decorate(
		array('class' => 'foo'), 
		array('id' => 'foo', 'class' => 'span5')
	);

	var_dump($attributes); // return array('class' => 'foo span5', 'id' => 'foo');
	
It also support replacement of default attributes if such requirement is needed.

	$attributes = Orchestra\Support\HTML::decorate(
		array('class' => 'foo !span5'),
		array('class' => 'bar span5'),
	);
	
	var_dump($attributes); // return array('class' => 'foo bar');