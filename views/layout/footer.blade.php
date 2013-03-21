<footer>
	<div class="container{{ (Orchestra\Site::has('layout::fixed') ? '' : '-fluid') }}">
		<hr>
		<p>&copy; 2012 Orchestra Platform</p>
	</div>
</footer>

<?php $asset = Asset::container('orchestra.backend: footer'); 

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

@placeholder("orchestra.layout: footer")
