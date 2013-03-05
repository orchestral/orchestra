@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	@include(locate('orchestra::layout.widgets.header'))
	@include(locate('orchestra::users.index.search'))
	{{ $table }}

</div>

<script>
jQuery(function($) {
	$('select').select2();
});
</script>

@endsection