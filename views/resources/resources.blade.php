@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	<div class="span2 well well-resources">
		<ul class="nav nav-list">
			<li class="nav-header">Resources</li>

			@foreach ($resources_list as $name => $resource)
			<li>{{ HTML::link(handles("orchestra::resources/{$name}"), $resource->name) }}</li>
			@endforeach
		</ul>
	</div>
	<div class="span10">
		@if ( ! empty($page_name))
		<div class="page-header">
			<h2>{{ $page_name }} 
				@if ( ! empty($page_desc))
				<small>{{ $page_desc ?: '' }}</small>
				@endif
			</h2>
		</div>
		@endif

		{{ $content }}
	</div>
</div>

@endsection