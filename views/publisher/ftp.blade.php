@layout(locate('orchestra::layout.main'))

@section('content')
<div class="row-fluid">
	<div class="span8">
		<div class="page-header">
			<h2>FTP Credential</h2>
		</div>

		<p>We would need to have your FTP credential in order to run the following task.</p>

		{{ Form::open(handles('orchestra::publisher/ftp'), 'POST', array('class' => 'form-horizontal')) }}
			<fieldset>
				<div class="control-group {{ $errors->has('host') ? 'error' : '' }}">
					{{ Form::label('host', __('orchestra::label.extensions.publisher.host'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::text('host', '', array('class' => 'input-xxlarge')) }}
						{{ $errors->first('host', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('user') ? 'error' : '' }}">
					{{ Form::label('user', __('orchestra::label.extensions.publisher.user'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::text('user', '', array('class' => 'input-xxlarge')) }}
						{{ $errors->first('user', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
					{{ Form::label('password', __('orchestra::label.extensions.publisher.password'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::password('password', array('class' => 'input-xxlarge')) }}
						{{ $errors->first('password', '<p class="help-block">:message</p>') }}
					</div>
				</div>

				<div class="control-group">
					{{ Form::label('connection-type', __('orchestra::label.extensions.publisher.connection-type'), array('class' => 'control-label')) }}
					<div class="controls">
						{{ Form::select('connection-type', array('ftp' => 'FTP', 'sftp' => 'SFTP'), 'ftp', array('role' => 'switcher')) }}
					</div>
				</div>
			</fieldset>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Login</button>
			</div>
		{{ Form::close() }}
	</div>

	<div class="span4">

	</div>
</div>
@endsection