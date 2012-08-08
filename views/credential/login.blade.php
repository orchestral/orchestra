@layout('orchestra::layout.main')

@section('content')

<div class="row">

	<div class="span12 form-horizontal">
		
		{{ Form::open(handles('orchestra::login'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::hidden('redirect', $redirect) }}
			{{ Form::token() }}
			<fieldset>
				<legend>{{ __('orchestra::title.login') }}</legend>
				
				<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
					{{ Form::label('email', __('orchestra::label.users.email'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('email', 'email', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('email', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
					{{ Form::label('password', __('orchestra::label.users.password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'password', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('password', '<p class="help-block">:message</p>') }}
					</div>
				</div>


			</fieldset>

			<div class="form-actions clean">
				<button type="submit" class="btn btn-primary">Submit</button> 
				{{ HTML::link(handles('orchestra::forgot'), __('orchestra::title.forgot-password')) }}
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection