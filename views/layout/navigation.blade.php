<?php 

$navbar = new Orchestra\Fluent(array(
	'id'             => 'main',
	'title'          => memorize('site.name', 'Orchestra'),
	'url'            => handles('orchestra'),
	'attributes'     => array('class' => 'navbar-fixed-top'),
	'primary_menu'   => render(locate('orchestra::layout.widgets.menu'), array('menu' => Orchestra\Core::menu())),
	'secondary_menu' => render(locate('orchestra::layout.widgets.usernav'), get_defined_vars()),
)); ?>

{{ Orchestra\Decorator::navbar($navbar) }}

@if ( ! Auth::check())

<script>
jQuery(function ($) {
	$('a[rel="user-menu"]').on('click', function (e) {
		e.preventDefault();
		
		window.location.href = "{{ URL::to(handles('orchestra::login')) }}";

		return false;
	});
});
</script>

@endif

<br>