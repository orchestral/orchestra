<?php

$title       = Orchestra\Site::get('title');
$description = Orchestra\Site::get('description'); ?>

<div class="page-header">
	<h2>{{ $title ?: 'Something Awesome' }}
		@if ( ! empty($description))
		<small>{{ $description ?: '' }}</small>
		@endif
	</h2>
</div>