@layout('orchestra::layout.main')

@section('content')

<div class="row">

	<div class="span12 form-horizontal">
		
		{{ Form::open(handles('orchestra::forgot'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::token() }}
			<fieldset>
				<legend>{{ __('orchestra::title.forgot-password') }}</legend>
				
				<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
					{{ Form::label('email', __('orchestra::label.users.email'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('email', 'email', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('email', '<p class="help-block">:message</p>') }}
					</div>
				</div>

			</fieldset>

			<div class="form-actions clean">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection