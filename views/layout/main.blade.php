<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{ $orchestra_memory->get('site.name', 'Orchestra') }}</title>
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
		
		$asset->style('bootstrap-responsive', 'bundles/orchestra/vendor/bootstrap/bootstrap-responsive.min.css', array('bootstrap'));
		$asset->style('select2', 'bundles/orchestra/vendor/select2/select2.css');
		$asset->script('select2', 'bundles/orchestra/vendor/select2/select2.min.js', array('jquery'));

		echo $asset->styles();
		echo $asset->scripts(); ?>
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

	</body>
</html>