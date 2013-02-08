@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span6 offset3 guest-form">

		<div class="page-header">
			<h3>{{ ! empty($_title_) ? $_title_ : 'Something Awesome Without A Name' }}
				@if ( ! empty($_description_))
				<small>{{ $_description_ ?: '' }}</small>
				@endif
			</h3>
		</div>

		{{ $form }}

	</div>

</div>

@endsection
