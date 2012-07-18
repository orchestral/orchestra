@layout('orchestra::layout.main')

@section('content')

<div class="row">

	<div class="span12 form-horizontal">
		
		{{ Form::open(handles('orchestra::account/password'), 'POST', array('class' => 'form-horizontal')) }}

			<fieldset>
				<legend>Edit Password</legend>
				
				<div class="control-group {{ $errors->has('current_password') ? 'error' : '' }}">
					{{ Form::label('current_password', __('orchestra::label.account.current_password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'current_password', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('current_password', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('new_password') ? 'error' : '' }}">
					{{ Form::label('new_password', __('orchestra::label.account.new_password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'new_password', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('new_password', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('confirm_password') ? 'error' : '' }}">
					{{ Form::label('confirm_password', __('orchestra::label.account.confirm_password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('password', 'confirm_password', '', array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('confirm_password', '<p class="help-block">:message</p>') }}
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