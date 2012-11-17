@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	<div class="span2 well-resources">
		<ul class="nav nav-tabs nav-stacked">
			@foreach ($resources_list as $name => $resource)
			<li class="{{ URI::is("*/resources/{$name}*") ? 'active' : '' }}">
				<a href="{{ handles("orchestra::resources/{$name}") }}">
					<i class="icon-chevron-right"></i>
					{{ $resource->name }}
				</a>
			</li>
			@endforeach
		</ul>
	</div>
	<div class="span10">
		@if ( ! empty($_title_))
		<div class="page-header">
			<h2>{{ ! empty($_title_) ? $_title_ : 'Something Awesome Without A Name' }} 
				@if ( ! empty($_description_))
				<small>{{ $_description_ ?: '' }}</small>
				@endif
			</h2>
		</div>
		@endif

		{{ $content }}
	</div>
</div>

@endsection