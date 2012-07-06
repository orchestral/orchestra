@layout('orchestra::layout.main')

@section('content')

<div class="row">
	<div class="span3">
		
		<div class="well" style="padding: 8px 0;">
			<ul class="nav nav-list">
				<li class="nav-header">Installation Process</li>
				<li>
					{{ HTML::link('orchestra/installer', '1. Verify Database Configuration') }}
				</li>
				<li class="active">
					{{ HTML::link('orchestra/installer/steps/1', '2. Create Administrator Account') }}
				</li>
			</ul>
		</div>

	</div>

	<div class="span8 form-horizontal">
		
		{{ Form::open(URL::to_action('orchestra::installer@steps', array(2)), 'POST', array('class' => 'form-horizontal')) }}

		<fieldset>
			<legend>Create Administrator User Account</legend>

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

			<div class="control-group {{ $errors->has('fullname') ? 'error' : '' }}">
				{{ Form::label('fullname', __('orchestra::label.users.fullname'), array('class' => 'control-label')) }}
				<div class="controls">
					{{ Form::input('text', 'fullname', '', array('required' => true, 'class' => 'span4')) }}
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