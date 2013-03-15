@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span6 offset3 guest-form">
		
		@include(locate('orchestra::layout.widgets.header'))

		{{ Form::open(handles('orchestra::forgot'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::token() }}
			<fieldset>

				<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
					{{ Form::label('email', __('orchestra::label.users.email'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('email', 'email', '', array('required' => true, 'class' => 'span12')) }}
						{{ $errors->first('email', '<p class="help-block">:message</p>') }}
					</div>
				</div>

			</fieldset>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">{{ Orchestra\Site::get('title', 'Submit') }}</button>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
