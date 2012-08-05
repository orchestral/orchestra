<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{ $memory->get('site.name', 'Orchestra') }}</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Le styles -->
		<?php 

		$asset = Asset::container('orchestra.backend');

		$asset->style('bootstrap', 'bundles/orchestra/css/bootstrap.min.css');
		$asset->style('style', 'bundles/orchestra/css/style.css', array('bootstrap'));
		$asset->style('select2', 'bundles/orchestra/js/select2/select2.css');

		$asset->script('jquery', 'bundles/orchestra/js/jquery.min.js');
		$asset->script('bootstrap', 'bundles/orchestra/js/bootstrap.min.js', array('jquery'));
		$asset->script('select2', 'bundles/orchestra/js/select2/select2.min.js', array('jquery'));

		echo $asset->styles();
		echo $asset->scripts(); ?>
	</head>

	<body>
		
		@include('orchestra::layout.widgets.navigation')

		<section class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">

			<?php $message = Orchestra\Messages::retrieve(); ?>

			@if ($message instanceof Orchestra\Messages)

			@foreach (array('error', 'info', 'success') as $key)
			@if ($message->has($key))
				<?php 

				$message->format('<div class="alert alert-'.$key.'">:message<button class="close" data-dismiss="alert">×</button></div>'); ?>
				{{ implode('', $message->get($key)) }}
			@endif
			@endforeach

			@endif

			@yield('content')

		</section>

		<footer>
			<div class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">
				<hr>
				<p>&copy; 2012 Orchestra</p>
			</div>
		</footer>
	<script>
	jQuery(function($) {
		$('div.btn-group[data-toggle-name=*]').each(function() {
			var group, form, name, hidden, buttons;

			group   = $(this);
			form    = group.parents('form').eq(0);
			name    = group.attr('data-toggle-name');
			hidden  = $('input[name="' + name + '"]', form);
			buttons = $('button', group);

			buttons.each(function(){
				var button, setActive;

				button = $(this);

				setActive = function setActive() {
					if(button.val() == hidden.val()) {
						button.addClass('active');
					}
				};
				
				button.live('click', function() {
					buttons.removeClass('active');

					hidden.val($(this).val());

					setActive();
				});

				setActive();
			});
		});
	});
	</script>

	</body>
</html>