# HTML Helper Class

## Table of Contents

* [Create HTML Tag](#create)
* [Raw HTML entities](#raw)
* [Decorate HTML](#decorate)

<a name="create"></a>
## Create HTML Tag

Create a HTML tag from within your libraries/extension using following code:

	{{ Orchestra\HTML::create('p', 'Some awesome information') }}
	
	// return <p>Some awesome information</p> 

Customize the HTML attibutes by adding third parameter.

	{{ Orchestra\HTML::create('p', 'Another awesomeness', array('id' => 'foo')) }}
	
	// return <p id="foo">Another awesomeness</p>

<a name="raw"></a>
## Raw HTML entities

Mark a string to be excluded from being escaped.

	{{ HTML::link('foo', Orchestra\HTML::raw('<img src="foo.jpg">')) }}
	
	// return <a href="foo"><img src="foo.jpg"></a>

<a name="decorate"></a>
## Decorate HTML

Decorate method allow developer to define HTML attributes collection as `HTML::attributes` method, with the addition of including default attributes array as second parameter.

	$attributes = Orchestra\HTML::decorate(
		array('class' => 'foo'), 
		array('id' => 'foo', 'class' => 'span5')
	);

	// return array('class' => 'foo span5', 'id' => 'foo');
	
It also support replacement of default attributes if such requirement is needed.

	$attributes = Orchestra\HTML::decorate(
		array('class' => 'foo !span5'),
		array('class' => 'bar span5'),
	);
	
	// return array('class' => 'foo bar');