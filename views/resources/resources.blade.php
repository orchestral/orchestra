@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span2 well-resources">
		<ul class="nav nav-tabs nav-stacked">
			@foreach ($resources['list'] as $name => $resource)
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
		{{ $content }}
	</div>
	
</div>

@endsection