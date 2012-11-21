<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{ HTML::title(isset($_title_) ? $_title_ : '') }}</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Le styles -->
		<?php

		$backend = Asset::container('orchestra.backend');
		$bottom  = Asset::container('orchestra.backend: bottom');

		$backend->style('bootstrap', 'bundles/orchestra/vendor/bootstrap/bootstrap.min.css');
		$backend->style('bootstrap-responsive', 'bundles/orchestra/vendor/bootstrap/bootstrap-responsive.min.css', array('bootstrap'));
		$backend->style('orchestra', 'bundles/orchestra/css/style.css', array('bootstrap-responsive'));

		$backend->style('select2', 'bundles/orchestra/vendor/select2/select2.css');
		$backend->style('jquery-ui', 'bundles/orchestra/vendor/delta/theme/jquery-ui.css');

		$bottom->script('select2', 'bundles/orchestra/vendor/select2/select2.min.js', array('jquery'));

		// Add jQuery-UI Library with Delta theme.
		$bottom->script('jquery-ui', 'bundles/orchestra/vendor/jquery.ui.js', array('jquery'));
		$bottom->script('jquery-ui-ts', 'bundles/orchestra/vendor/delta/js/jquery-ui.toggleSwitch.js', array('jquery-ui')); ?>

		{{ $backend->styles() }}
		{{ $backend->scripts() }}

	</head>

	<body>

		@include('orchestra::layout.widgets.navigation')

		<section class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">

			@include('orchestra::layout.widgets.messages')

			@yield('content')

		</section>

		<footer>
			<div class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">
				<hr>
				<p>&copy; 2012 Orchestra</p>
			</div>
		</footer>

		{{ $bottom->styles() }}
		{{ $bottom->scripts() }}
	</body>
</html>
