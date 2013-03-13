<meta charset="utf-8">
{{ HTML::title() }}
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
$asset->style('orchestra', 'bundles/orchestra/css/style.css', array('bootstrap-responsive')); ?>

{{ $asset->styles() }}
{{ $asset->scripts() }}

@placeholder("orchestra.layout: header")
