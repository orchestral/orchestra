@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">

	<div class="span6 offset3">

		{{ Form::open(handles('orchestra::login'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::hidden('redirect', $redirect) }}
			{{ Form::token() }}
			<fieldset>
				<legend>{{ __('orchestra::title.login') }}</legend>

				<div class="control-group {{ $errors->has('username') ? 'error' : '' }}">
					{{ Form::label('username', __("orchestra::label.users.{$username_types}"), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('text', 'username', '', array('required' => true, 'class' => 'input-xlarge')) }}
						{{ $errors->first('username', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
					{{ Form::label('password', __('orchestra::label.users.password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'password', '', array('required' => true, 'class' => 'input-xlarge')) }}
						{{ $errors->first('password', '<p class="help-block">:message</p>') }}
					</div>
				</div>

			</fieldset>

			<div class="form-actions clean">
				<button type="submit" class="btn btn-primary">{{ __('orchestra::title.login') }}</button>
				{{ HTML::link(handles('orchestra::forgot'), __('orchestra::title.forgot-password'), array('class' => 'btn btn-link')) }}
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
