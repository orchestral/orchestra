@layout('orchestra::layout.main')

@section('content')

<div class="rows">

	<div class="page-header">
		<h2>{{ $resource_name }}</h2>
	</div>

	{{ $form }}
</div>

<script>
jQuery(function($) {
	$('select').select2();
});
</script>

@endsection