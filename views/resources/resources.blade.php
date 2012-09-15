@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	<div class="span2 well well-resources">
		<ul class="nav nav-list">
			<li class="nav-header">Resources</li>

			@foreach ($resources as $name => $resource)
			<li>{{ HTML::link(handles("orchestra::resources/{$name}"), $resource->name) }}</li>
			@endforeach
		</ul>
	</div>
	<div class="span10">
		{{ $content }}
	</div>
</div>

@endsection