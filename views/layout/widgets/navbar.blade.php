{{-- Define the navbar attributes --}}
<?php $attributes = Orchestra\HTML::decorate($navbar->attributes ?: array(), array('class' => 'navbar')); ?>

<div{{ Orchestra\HTML::attributes($attributes) }}>
	<div class="navbar-inner">
		<div class="container{{ (Orchestra\Site::has('layout::fixed') ? '' : '-fluid') }}">

			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#{{ $navbar->id }}nav">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			{{ HTML::link($navbar->url, $navbar->title, array('class' => 'brand')) }}

			<div id="{{ $navbar->id }}nav" class="collapse nav-collapse">
					
				{{ $navbar->primary_menu }}
				{{ $navbar->secondary_menu }}

			</div>
		</div>
	</div>
</div>