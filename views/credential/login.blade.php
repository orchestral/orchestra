@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">

	<div class="span6 offset3">

		<div class="page-header">
			<h3>{{ ! empty($_title_) ? $_title_ : 'Something Awesome Without A Name' }}
				@if ( ! empty($_description_))
				<small>{{ $_description_ ?: '' }}</small>
				@endif
			</h3>
		</div>

		{{ Form::open(handles('orchestra::login'), 'POST', array('class' => 'form-horizontal')) }}
			{{ Form::hidden('redirect', $redirect) }}
			{{ Form::token() }}
			<fieldset>

				<div class="control-group {{ $errors->has('username') ? 'error' : '' }}">
					{{ Form::label('username', __("orchestra::label.users.{$username_types}"), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('text', 'username', '', array('required' => true, 'class' => 'input-xlarge', 'tabindex' => 1)) }}
						{{ $errors->first('username', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
					{{ Form::label('password', __('orchestra::label.users.password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'password', '', array('required' => true, 'class' => 'input-xlarge', 'tabindex' => 2)) }}
						{{ $errors->first('password', '<p class="help-block">:message</p>') }}
						<p class="help-block">
							{{ HTML::link(handles('orchestra::forgot'), __('orchestra::title.forgot-password')) }}
						</p>
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
					<label class="checkbox">
						{{ Form::checkbox('remember', 'yes', false, array('tabindex' => 3)) }} 
						{{ __('orchestra::title.remember-me') }}
					</label>
				</div>

			</fieldset>

			<div class="form-actions clean">
				<button type="submit" class="btn btn-primary">{{ __('orchestra::title.login') }}</button>
				@if(memorize('site.users.registration', false))
				{{ HTML::link(handles('orchestra::register'), __('orchestra::title.register'), array('class' => 'btn')) }}
				@endif
			</div>

			
		{{ Form::close() }}

	</div>

</div>

@endsection
