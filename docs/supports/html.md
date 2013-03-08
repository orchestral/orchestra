# HTML Helper Class

## Decorate

Decorate method allow developer to define HTML attributes collection as well as specifying the default.

	$finalize = Orchestra\Support\HTML::decorate(array('class' => 'foo'), array('id' => 'foo', 'class' => 'span5'));

	var_dump($finalize); // return array('class' => 'foo span5', 'id' => 'foo');