@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span6 offset3 guest-form">

		@include(locate('orchestra::layout.widgets.header'))

		{{ $form }}

	</div>

</div>

@endsection
