@layout('orchestra::layout.main')

@section('content')

<div class="row">

	<div class="span12 form-horizontal">
		
		{{ Form::open(handles('orchestra::account'), 'POST', array('class' => 'form-horizontal')) }}

			<fieldset>
				<legend>User Profile</legend>
				
				<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
					{{ Form::label('email', __('orchestra::label.users.email'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('email', 'email', $user->email, array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('email', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('fullname') ? 'error' : '' }}">
					{{ Form::label('fullname', __('orchestra::label.users.fullname'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::input('text', 'fullname', $user->fullname, array('required' => true, 'class' => 'span4')) }}
						{{ $errors->first('fullname', '<p class="help-block">:message</p>') }}
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