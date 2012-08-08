@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	
	<div class="page-header">
		<h2>{{ $resource_name }}</h2>
	</div>

	{{ Form::open(handles('orchestra::users'), 'GET', array('class' => 'well form-inline')) }}
		<div class="pull-left">
			&nbsp;{{ Form::text('q', Input::get('q'), array('placeholder' => __('orchestra::label.search.keyword'), 'class' => 'input-xlarge'))}}
		</div>
		<div class="pull-left">
			&nbsp;{{ Form::select('roles[]', $roles, Input::get('roles'), array('multiple' => true, 'placeholder' => __('orchestra::label.users.roles'), 'class' => 'input-xlarge'))}}
		</div>
		<div class="pull-left">
			&nbsp;{{ Form::button(__('orchestra::label.search.button'), array('type' => 'submit', 'class' => 'btn btn-primary')) }}
		</div>

	{{ Form::close() }}

	<br>

	{{ $table }}

</div>

<script>
jQuery(function($) {
	$('select').select2();
});
</script>

@endsection