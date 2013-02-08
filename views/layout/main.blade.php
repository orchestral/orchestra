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

		$asset = Asset::container('orchestra.backend');

		$asset->script('underscore', 'bundles/orchestra/js/underscore.min.js');
		$asset->script('jquery', 'bundles/orchestra/js/jquery.min.js');
		$asset->script('javie', 'bundles/orchestra/js/javie.min.js', array('underscore'));

		$asset->style('bootstrap', 'bundles/orchestra/vendor/bootstrap/bootstrap.min.css');
		$asset->style('bootstrap-responsive', 'bundles/orchestra/vendor/bootstrap/bootstrap-responsive.min.css', array('bootstrap'));
		$asset->style('orchestra', 'bundles/orchestra/css/style.css', array('bootstrap-responsive'));

		$asset->style('select2', 'bundles/orchestra/vendor/select2/select2.css');
		$asset->style('jquery-ui', 'bundles/orchestra/vendor/delta/theme/jquery-ui.css');

		$asset->script('bootstrap', 'bundles/orchestra/vendor/bootstrap/bootstrap.min.js', array('jquery'));
		$asset->script('orchestra', 'bundles/orchestra/js/script.min.js', array('bootstrap', 'javie'));
		$asset->script('select2', 'bundles/orchestra/vendor/select2/select2.min.js', array('jquery'));

		// Add jQuery-UI Library with Delta theme.
		$asset->script('jquery-ui', 'bundles/orchestra/vendor/jquery.ui.js', array('jquery'));
		$asset->script('jquery-ui-ts', 'bundles/orchestra/vendor/delta/js/jquery-ui.toggleSwitch.js', array('jquery-ui')); ?>

		{{ $asset->styles() }}
		{{ $asset->scripts() }}

	</head>

	<body>

		@include(locate('orchestra::layout.widgets.navigation'))

		<section class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">

			@include(locate('orchestra::layout.widgets.messages'))

			@yield('content')

		</section>

		<footer>
			<div class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">
				<hr>
				<p>&copy; 2012 Orchestra</p>
			</div>
		</footer>

		<?php $asset = Asset::container('orchestra.backend: bottom'); ?>
		{{ $asset->styles() }}
		{{ $asset->scripts() }}
	</body>
</html>
