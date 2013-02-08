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

		{{ Form::open(handles('orchestra::forgot'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::token() }}
			<fieldset>

				<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
					{{ Form::label('email', __('orchestra::label.users.email'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('email', 'email', '', array('required' => true, 'class' => 'input-xlarge')) }}
						{{ $errors->first('email', '<p class="help-block">:message</p>') }}
					</div>
				</div>

			</fieldset>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">{{ $_title_ }}</button>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
