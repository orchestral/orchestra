@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span12">
		@include(locate('orchestra::layout.widgets.header'))
		{{ $form }}
	</div>

</div>

@endsection
