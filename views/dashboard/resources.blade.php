@layout('orchestra::layout.main-fluent')

@section('content')

<div class="rows">

	<div class="page-header">
		<h2>{{ $resource_name }}</h2>
	</div>

	{{ $table }}
</div>

@endsection