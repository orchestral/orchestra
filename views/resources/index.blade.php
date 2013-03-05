@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">
	@include(locate('orchestra::layout.widgets.header'))
	{{ $table }}
</div>

@endsection