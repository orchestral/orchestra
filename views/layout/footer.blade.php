<footer>
	<div class="container{{ (Orchestra\Site::has('layout::fixed') ? '' : '-fluid') }}">
		<hr>
		<p>&copy; 2012 Orchestra</p>
	</div>
</footer>

<?php $asset = Asset::container('orchestra.backend: footer'); ?>
{{ $asset->styles() }}
{{ $asset->scripts() }}